<?php
header('Content-Type: application/json');

// Incluir arquivos necessários
require_once("sistema/conexao.php");
require_once 'efi/boleto.php';

// Configurações
$options = require_once 'efi/options.php';
$config = [
    'client_id' => $options['clientId'],
    'client_secret' => $options['clientSecret'],
    'certificate_path' => $options['certificate'], // Apenas para PIX
    'chave_pix' => env('EFI_PIX_KEY', $chave_pix ?? ''), // Sua chave PIX
    'sandbox' => $options['sandbox'] // true para teste, false para produção
];

// Função para log de mensagens
function logMessage($message, $errorLogFile = null)
{
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] $message" . PHP_EOL;

    if ($errorLogFile) {
        error_log($logEntry, 3, $errorLogFile);
    } else {
        error_log($message);
    }
}

// Função para log de webhook
function logWebhook($pdo, $eventType, $payload, $receivedAt)
{
    try {
        $stmt = $pdo->prepare("INSERT INTO webhook_logs (event_type, payload, received_at) VALUES (?, ?, ?)");
        $stmt->execute([$eventType, $payload, $receivedAt]);
        return true;
    } catch (PDOException $e) {
        error_log("Erro ao salvar log: " . $e->getMessage());
        return false;
    }
}

// Função para atualizar status do pagamento boleto
function atualizarStatusPagamentoBoleto($pdo, $chargeId, $status)
{
    try {
        $stmt = $pdo->prepare("UPDATE pagamentos_boleto SET status = ? WHERE charge_id = ?");
        return $stmt->execute([$status, $chargeId]);
    } catch (PDOException $e) {
        error_log("Erro ao atualizar pagamento boleto: " . $e->getMessage());
        return false;
    }
}

// Função para buscar id_matricula pelo charge_id
function buscarIdMatriculaBoleto($pdo, $chargeId)
{
    try {
        $stmt = $pdo->prepare("SELECT id_matricula FROM pagamentos_boleto WHERE charge_id = :id OR id_asaas = :id LIMIT 1");
        $stmt->execute([':id' => $chargeId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['id_matricula'] : null;
    } catch (PDOException $e) {
        error_log("Erro ao buscar id_matricula: " . $e->getMessage());
        return null;
    }
}

// Função para atualizar status da matrícula
function atualizarStatusMatricula($pdo, $idMatricula, $status)
{
    try {
        $stmt = $pdo->prepare("UPDATE matriculas SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $idMatricula]);
    } catch (PDOException $e) {
        error_log("Erro ao atualizar matrícula: " . $e->getMessage());
        return false;
    }
}

// Função para verificar se matrícula é um pacote e buscar dados necessários
function verificarDadosMatricula($pdo, $idMatricula)
{
    try {
        $stmt = $pdo->prepare("SELECT id_curso, aluno, pacote FROM matriculas WHERE id = ?");
        $stmt->execute([$idMatricula]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erro ao verificar dados da matrícula: " . $e->getMessage());
        return null;
    }
}

// Função para verificar se curso é um pacote
function verificarSeCursoEPacote($pdo, $idMatricula)
{
    try {
        $stmt = $pdo->prepare("SELECT pacote FROM matriculas WHERE id = ?");
        $stmt->execute([$idMatricula]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['pacote'] : null;
    } catch (PDOException $e) {
        error_log("Erro ao verificar se curso é pacote: " . $e->getMessage());
        return null;
    }
}

// Auxiliares para localizar e atualizar matrículas em qualquer tabela
function localizarMatriculaGeral($pdo, $idMatricula)
{
    $tabelas = ['matriculas', 'matriculas_tecnicos', 'matriculas_profissionalizantes'];
    foreach ($tabelas as $tabela) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM {$tabela} WHERE id = ?");
            $stmt->execute([$idMatricula]);
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($res) {
                return ['tabela' => $tabela, 'dados' => $res];
            }
        } catch (PDOException $e) {
            error_log("Erro ao consultar {$tabela}: " . $e->getMessage());
        }
    }
    return null;
}

function atualizarStatusMatriculaGeral($pdo, $tabela, $idMatricula, $status, $forma_pgto = null)
{
    $tabelasPermitidas = ['matriculas', 'matriculas_tecnicos', 'matriculas_profissionalizantes'];
    if (!in_array($tabela, $tabelasPermitidas, true)) {
        error_log("Tabela de matricula não permitida: {$tabela}");
        return false;
    }

    try {
        $sql = "UPDATE {$tabela} SET status = :status";
        if ($forma_pgto !== null) {
            $sql .= ", forma_pgto = :forma_pgto";
        }
        $sql .= " WHERE id = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':id', $idMatricula, PDO::PARAM_INT);
        if ($forma_pgto !== null) {
            $stmt->bindValue(':forma_pgto', $forma_pgto);
        }
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Erro ao atualizar matricula em {$tabela}: " . $e->getMessage());
        return false;
    }
}

// Função para ativar cursos do pacote
function ativarCursosDoPacote($pdo, $idCurso, $alunoId)
{
    try {
        logMessage("Matrícula é um pacote. Iniciando liberação automática dos cursos individuais.");

        // Desativa temporariamente o modo safe update
        $pdo->query("SET SQL_SAFE_UPDATES = 0");

        // Cria uma tabela temporária com os cursos do pacote
        $pdo->query("CREATE TEMPORARY TABLE temp_cursos_pacote AS
            SELECT 
                cp.id AS id_cursos_pacotes,
                cp.id_curso AS id_do_curso,
                c.matriculas,
                c.professor AS id_professor
            FROM 
                cursos_pacotes cp
            JOIN 
                cursos c ON cp.id_curso = c.id
            WHERE 
                cp.id_pacote = {$idCurso}");

        // Verifica se há registros na tabela temporária
        $query_count = $pdo->query("SELECT COUNT(*) FROM temp_cursos_pacote");
        $total_registros = $query_count->fetchColumn();

        logMessage("Encontrados {$total_registros} cursos no pacote {$idCurso}");

        if ($total_registros > 0) {
            $pdo->query("CREATE TEMPORARY TABLE temp_matriculas_existentes AS
                SELECT id_curso
                FROM matriculas
                WHERE aluno = {$alunoId} AND id_pacote = {$idCurso}");

            // Insere novas matrículas apenas para os cursos que o aluno não está matriculado ainda
            $stmt_insert_matriculas = $pdo->prepare("INSERT INTO matriculas 
                (id_curso, aluno, professor, aulas_concluidas, data, status, pacote, id_pacote, obs)
                SELECT 
                    tcp.id_do_curso,
                    :aluno_id,
                    tcp.id_professor,
                    1,
                    CURDATE(),
                    'Matriculado',
                    'Não',
                    :id_curso,
                    'Pacote'
                FROM 
                    temp_cursos_pacote tcp
                LEFT JOIN
                    temp_matriculas_existentes tme ON tcp.id_do_curso = tme.id_curso
                WHERE 
                    tme.id_curso IS NULL");

            $stmt_insert_matriculas->execute([
                ':aluno_id' => $alunoId,
                ':id_curso' => $idCurso
            ]);

            $novas_matriculas = $stmt_insert_matriculas->rowCount();
            logMessage("Adicionadas {$novas_matriculas} novas matrículas para os cursos do pacote");

            // Atualiza contador de matrículas apenas para os cursos onde novas matrículas foram adicionadas
            $pdo->query("UPDATE cursos c
                JOIN temp_cursos_pacote tcp ON c.id = tcp.id_do_curso
                LEFT JOIN temp_matriculas_existentes tme ON tcp.id_do_curso = tme.id_curso
                SET c.matriculas = c.matriculas + 1
                WHERE tme.id_curso IS NULL");

            // Limpa as tabelas temporárias
            $pdo->query("DROP TEMPORARY TABLE IF EXISTS temp_cursos_pacote");
            $pdo->query("DROP TEMPORARY TABLE IF EXISTS temp_matriculas_existentes");

            // Reativa o modo safe update
            $pdo->query("SET SQL_SAFE_UPDATES = 1");

            logMessage("Liberação automática de cursos do pacote concluída com sucesso");
            return true;
        } else {
            logMessage("Nenhum curso encontrado para este pacote");
            return false;
        }
    } catch (PDOException $e) {
        logMessage("Erro ao liberar cursos do pacote: " . $e->getMessage());
        return false;
    }
}

// Libera cursos vinculados a um profissionalizante (pacote) após pagamento
function ativarCursosProfissionalizantes($pdo, $idProfissionalizante, $alunoId)
{
    try {
        $stmtCursos = $pdo->prepare("
            SELECT cp.id_curso, c.professor
              FROM cursos_profissionalizantes cp
              JOIN cursos c ON c.id = cp.id_curso
             WHERE cp.id_profissionalizante = :id_prof
        ");
        $stmtCursos->execute([':id_prof' => $idProfissionalizante]);
        $cursos = $stmtCursos->fetchAll(PDO::FETCH_ASSOC);

        if (!$cursos) {
            logMessage("Nenhum curso vinculado ao profissionalizante {$idProfissionalizante}");
            return false;
        }

        foreach ($cursos as $curso) {
            $idCurso = $curso['id_curso'];
            $professorId = $curso['professor'];

            // Verifica se o aluno já tem matrícula deste curso (fora do pacote)
            $stmtExiste = $pdo->prepare("SELECT id FROM matriculas_profissionalizantes WHERE aluno = :aluno AND id_curso = :curso AND pacote = 'Não' LIMIT 1");
            $stmtExiste->execute([
                ':aluno' => $alunoId,
                ':curso' => $idCurso
            ]);
            $matriculaExistente = $stmtExiste->fetch(PDO::FETCH_ASSOC);

            if ($matriculaExistente) {
                $stmtUpdate = $pdo->prepare("
                    UPDATE matriculas_profissionalizantes
                       SET status = 'Matriculado',
                           forma_pgto = 'BOLETO',
                           obs = 'Pacote',
                           id_pacote = :id_pacote
                     WHERE id = :id
                ");
                $stmtUpdate->execute([
                    ':id_pacote' => $idProfissionalizante,
                    ':id' => $matriculaExistente['id']
                ]);
            } else {
                $stmtInsert = $pdo->prepare("
                    INSERT INTO matriculas_profissionalizantes
                        (id_curso, aluno, professor, aulas_concluidas, data, status, pacote, id_pacote, obs, forma_pgto)
                    VALUES
                        (:curso, :aluno, :professor, 1, CURDATE(), 'Matriculado', 'Não', :id_pacote, 'Pacote', 'BOLETO')
                ");
                $stmtInsert->execute([
                    ':curso' => $idCurso,
                    ':aluno' => $alunoId,
                    ':professor' => $professorId,
                    ':id_pacote' => $idProfissionalizante
                ]);

                $stmtContador = $pdo->prepare("UPDATE cursos SET matriculas = matriculas + 1 WHERE id = :curso");
                $stmtContador->execute([':curso' => $idCurso]);
            }
        }

        logMessage("Liberação de cursos vinculados ao profissionalizante {$idProfissionalizante} concluída");
        return true;
    } catch (PDOException $e) {
        logMessage("Erro ao liberar cursos do profissionalizante {$idProfissionalizante}: " . $e->getMessage());
        return false;
    }
}

// Processar webhook
try {


    // Verificar se é uma requisição POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['error' => 'Método não permitido']);
        exit;
    }

    // Lê o conteúdo bruto enviado
    $input = file_get_contents('php://input');

    // Tenta decodificar como JSON
    $data = json_decode($input, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        $data = ['raw' => $input];
    }



    // Prepara os dados para log
    $eventType = $data['event'] ?? $data['type'] ?? 'boleto_webhook';
    $payload = json_encode($data, JSON_UNESCAPED_UNICODE);
    $receivedAt = date('Y-m-d H:i:s');

    // Suporta dois formatos de notificação:
    // 1) JSON da API de cobranças com status/charge_id
    // 2) notification=hash (formato legado)
    $currentStatus = null;
    $chargeId = null;
    $lastItem = null;

    if (isset($data['charge_id']) && isset($data['status'])) {
        // Formato JSON direto
        $chargeId = $data['charge_id'];
        $currentStatus = $data['status'];
        $lastItem = $data;
        logMessage("Webhook JSON direto recebido. Charge ID: $chargeId, Status: $currentStatus");
    } else {
        // Formato notification=hash
        $notification = trim($input);

        if (strpos($notification, '=') === false) {
            http_response_code(400);
            echo json_encode(['error' => 'Formato de notificação inválido']);
            logWebhook($pdo, 'boleto_error', $payload, $receivedAt);
            exit;
        }

        list($key, $value) = explode('=', $notification, 2);
        $notification_hash = $value;
        $notification_hash = rtrim($notification_hash, '"');

        logMessage("Processando webhook de boleto. Notification hash: $notification_hash");

        // Consultar webhook da EFI
        $boletoWebhook = new EFIBoletoPayment(
            $config['client_id'],
            $config['client_secret'],
            $config['sandbox']
        );

        $result = $boletoWebhook->consultarWebhook($notification_hash);

        if (!$result || !isset($result['data']) || empty($result['data'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Dados do webhook inválidos']);
            logWebhook($pdo, 'boleto_error', json_encode(['error' => 'Dados inválidos', 'result' => $result]), $receivedAt);
            exit;
        }

        // Pega o último item do array (status mais recente)
        $lastItem = end($result['data']);
        $currentStatus = $lastItem['status']['current'];
        $chargeId = $lastItem['identifiers']['charge_id'];
    }

    logMessage("Status atual do boleto: $currentStatus, Charge ID: $chargeId");

    if ($currentStatus === 'paid') {
        // Boleto pago - processar pagamento
        $pdo->beginTransaction();

        try {
           
            // Atualizar status do pagamento boleto
            if (!atualizarStatusPagamentoBoleto($pdo, $chargeId, 'paid')) {
                throw new Exception("Falha ao atualizar pagamento boleto");
            }

            // Buscar id_matricula
            $idMatricula = buscarIdMatriculaBoleto($pdo, $chargeId);

            if ($idMatricula) {
                $matriculaInfo = localizarMatriculaGeral($pdo, $idMatricula);
                if ($matriculaInfo) {
                    $tabelaMatricula = $matriculaInfo['tabela'];
                    $dadosMatricula = $matriculaInfo['dados'];

                    if (!atualizarStatusMatriculaGeral($pdo, $tabelaMatricula, $idMatricula, 'Matriculado', 'BOLETO')) {
                        throw new Exception('Falha ao atualizar matrícula');
                    }

                    $pacoteFlag = isset($dadosMatricula['pacote']) ? $dadosMatricula['pacote'] : null;
                    $pacoteAtivo = $pacoteFlag !== null && strcasecmp($pacoteFlag, 'Sim') === 0;

                    if ($tabelaMatricula === 'matriculas' && $pacoteAtivo) {
                        $idCurso = $dadosMatricula['id_curso'];
                        $alunoId = $dadosMatricula['aluno'];

                        logMessage("Detectado pagamento de pacote via boleto. Charge ID: $chargeId, Curso: $idCurso, Aluno: $alunoId");

                        if (!ativarCursosDoPacote($pdo, $idCurso, $alunoId)) {
                            logMessage("Falha ao ativar cursos do pacote para Charge ID: $chargeId");
                        } else {
                            logMessage("Cursos do pacote ativados com sucesso para Charge ID: $chargeId");
                        }
                    }

                    if ($tabelaMatricula === 'matriculas_profissionalizantes' && $pacoteAtivo) {
                        $idPacoteProf = $dadosMatricula['id_curso'];
                        $alunoId = $dadosMatricula['aluno'];

                        logMessage("Detectado pagamento de pacote profissionalizante via boleto. Charge ID: $chargeId, Profissionalizante: $idPacoteProf, Aluno: $alunoId");

                        if (!ativarCursosProfissionalizantes($pdo, $idPacoteProf, $alunoId)) {
                            logMessage("Falha ao ativar cursos do profissionalizante para Charge ID: $chargeId");
                        } else {
                            logMessage("Cursos do profissionalizante ativados com sucesso para Charge ID: $chargeId");
                        }
                    }
                } else {
                    error_log("Matrícula não localizada em nenhuma tabela para Charge ID: $chargeId");
                }
            } else {
                error_log("ID da matrícula não encontrado para Charge ID: $chargeId");
            }

            $pdo->commit();

            // Log de sucesso
            logWebhook($pdo, 'boleto_paid', json_encode([
                'charge_id' => $chargeId,
                'id_matricula' => $idMatricula,
                'status' => 'processado_com_sucesso',
                'webhook_data' => $lastItem
            ]), $receivedAt);

        } catch (Exception $e) {
            $pdo->rollBack();
            error_log("Erro ao processar boleto pago: " . $e->getMessage());
            logWebhook($pdo, 'boleto_error', json_encode([
                'charge_id' => $chargeId,
                'error' => $e->getMessage()
            ]), $receivedAt);

            http_response_code(500);
            echo json_encode(['error' => 'Erro ao processar pagamento']);
            exit;
        }

    } else {
        // Status diferente de paid - apenas fazer log
        logWebhook($pdo, 'boleto_status_' . $currentStatus, json_encode([
            'charge_id' => $chargeId,
            'status' => $currentStatus,
            'dados_completos' => $lastItem
        ]), $receivedAt);

        logMessage("Boleto com status '$currentStatus' registrado no log. Charge ID: $chargeId");
    }

    // Resposta de sucesso
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'charge_id' => $chargeId,
        'status' => $currentStatus,
        'processed' => $currentStatus === 'paid'
    ]);

} catch (Exception $e) {
    error_log("Erro geral no processamento do webhook de boleto: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Erro interno do servidor']);

    // Log do erro
    logWebhook($pdo, 'boleto_error', json_encode([
        'error' => $e->getMessage(),
        'input' => $input ?? 'N/A'
    ]), date('Y-m-d H:i:s'));
}
?>
