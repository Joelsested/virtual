<?php
header('Content-Type: application/json');

// Incluir arquivos necessÃƒÆ’Ã‚Â¡rios
require_once("sistema/conexao.php");
require_once 'efi/boleto.php';

// ConfiguraÃƒÆ’Ã‚Â§ÃƒÆ’Ã‚Âµes
$options = require_once 'efi/options.php';
$config = [
    'client_id' => $options['clientId'],
    'client_secret' => $options['clientSecret'],
    'certificate_path' => $options['certificate'], // Apenas para PIX
    'chave_pix' => env('EFI_PIX_KEY', $chave_pix ?? ''), // Sua chave PIX
    'sandbox' => $options['sandbox'] // true para teste, false para produÃƒÆ’Ã‚Â§ÃƒÆ’Ã‚Â£o
];

// FunÃƒÆ’Ã‚Â§ÃƒÆ’Ã‚Â£o para log de mensagens
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

// FunÃƒÆ’Ã‚Â§ÃƒÆ’Ã‚Â£o para log de webhook
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

function tabelaTemColuna(PDO $pdo, string $tabela, string $coluna): bool
{
    static $cache = [];
    $chave = $tabela . '.' . $coluna;
    if (array_key_exists($chave, $cache)) {
        return $cache[$chave];
    }

    try {
        $sql = "SELECT COUNT(*) FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = :tabela
                  AND COLUMN_NAME = :coluna";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':tabela' => $tabela,
            ':coluna' => $coluna
        ]);
        $cache[$chave] = ((int) $stmt->fetchColumn()) > 0;
        return $cache[$chave];
    } catch (PDOException $e) {
        error_log("Erro ao verificar coluna {$chave}: " . $e->getMessage());
        $cache[$chave] = false;
        return false;
    }
}

// FunÃ§Ã£o para atualizar status do pagamento boleto
function atualizarStatusPagamentoBoleto($pdo, $chargeId, $status)
{
    try {
        $campos = ["situacao = :situacao"];
        $params = [
            ':situacao' => ($status === 'paid') ? 1 : 0,
            ':id' => $chargeId
        ];

        if (tabelaTemColuna($pdo, 'parcelas_geradas_por_boleto', 'status')) {
            $campos[] = "status = :status";
            $params[':status'] = $status;
        }

        if ($status === 'paid' && tabelaTemColuna($pdo, 'parcelas_geradas_por_boleto', 'data_pagamento')) {
            $campos[] = "data_pagamento = COALESCE(data_pagamento, NOW())";
        }

        $sql = "UPDATE parcelas_geradas_por_boleto SET " . implode(', ', $campos) . " WHERE charge_id = :id OR id_asaas = :id";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    } catch (PDOException $e) {
        error_log("Erro ao atualizar pagamento boleto: " . $e->getMessage());
        return false;
    }
}
function buscarIdMatriculaBoleto($pdo, $chargeId)
{
    try {
        $stmt = $pdo->prepare("SELECT id_matricula FROM parcelas_geradas_por_boleto WHERE charge_id = :id OR id_asaas = :id LIMIT 1");
        $stmt->execute([':id' => $chargeId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['id_matricula'] : null;
    } catch (PDOException $e) {
        error_log("Erro ao buscar id_matricula: " . $e->getMessage());
        return null;
    }
}

// FunÃƒÆ’Ã‚Â§ÃƒÆ’Ã‚Â£o para atualizar status da matrÃƒÆ’Ã‚Â­cula
function atualizarStatusMatricula($pdo, $idMatricula, $status)
{
    try {
        $stmt = $pdo->prepare("UPDATE matriculas SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $idMatricula]);
    } catch (PDOException $e) {
        error_log("Erro ao atualizar matrÃƒÆ’Ã‚Â­cula: " . $e->getMessage());
        return false;
    }
}

// FunÃƒÆ’Ã‚Â§ÃƒÆ’Ã‚Â£o para verificar se matrÃƒÆ’Ã‚Â­cula ÃƒÆ’Ã‚Â© um pacote e buscar dados necessÃƒÆ’Ã‚Â¡rios
function verificarDadosMatricula($pdo, $idMatricula)
{
    try {
        $stmt = $pdo->prepare("SELECT id_curso, aluno, pacote FROM matriculas WHERE id = ?");
        $stmt->execute([$idMatricula]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erro ao verificar dados da matrÃƒÆ’Ã‚Â­cula: " . $e->getMessage());
        return null;
    }
}

// FunÃƒÆ’Ã‚Â§ÃƒÆ’Ã‚Â£o para verificar se curso ÃƒÆ’Ã‚Â© um pacote
function verificarSeCursoEPacote($pdo, $idMatricula)
{
    try {
        $stmt = $pdo->prepare("SELECT pacote FROM matriculas WHERE id = ?");
        $stmt->execute([$idMatricula]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['pacote'] : null;
    } catch (PDOException $e) {
        error_log("Erro ao verificar se curso ÃƒÆ’Ã‚Â© pacote: " . $e->getMessage());
        return null;
    }
}

// Auxiliares para localizar e atualizar matrÃƒÆ’Ã‚Â­culas em qualquer tabela
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
        error_log("Tabela de matricula nÃƒÆ’Ã‚Â£o permitida: {$tabela}");
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

// FunÃƒÆ’Ã‚Â§ÃƒÆ’Ã‚Â£o para ativar cursos do pacote
function ativarCursosDoPacote($pdo, $idCurso, $alunoId)
{
    try {
        logMessage("MatrÃƒÆ’Ã‚Â­cula ÃƒÆ’Ã‚Â© um pacote. Iniciando liberaÃƒÆ’Ã‚Â§ÃƒÆ’Ã‚Â£o automÃƒÆ’Ã‚Â¡tica dos cursos individuais.");

        // Desativa temporariamente o modo safe update
        $pdo->query("SET SQL_SAFE_UPDATES = 0");

        // Cria uma tabela temporÃƒÆ’Ã‚Â¡ria com os cursos do pacote
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

        // Verifica se hÃƒÆ’Ã‚Â¡ registros na tabela temporÃƒÆ’Ã‚Â¡ria
        $query_count = $pdo->query("SELECT COUNT(*) FROM temp_cursos_pacote");
        $total_registros = $query_count->fetchColumn();

        logMessage("Encontrados {$total_registros} cursos no pacote {$idCurso}");

        if ($total_registros > 0) {
            $pdo->query("CREATE TEMPORARY TABLE temp_matriculas_existentes AS
                SELECT id_curso
                FROM matriculas
                WHERE aluno = {$alunoId} AND id_pacote = {$idCurso}");

            // Insere novas matrÃƒÆ’Ã‚Â­culas apenas para os cursos que o aluno nÃƒÆ’Ã‚Â£o estÃƒÆ’Ã‚Â¡ matriculado ainda
            $stmt_insert_matriculas = $pdo->prepare("INSERT INTO matriculas 
                (id_curso, aluno, professor, aulas_concluidas, data, status, pacote, id_pacote, obs)
                SELECT 
                    tcp.id_do_curso,
                    :aluno_id,
                    tcp.id_professor,
                    1,
                    CURDATE(),
                    'Matriculado',
                    'NÃƒÆ’Ã‚Â£o',
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
            logMessage("Adicionadas {$novas_matriculas} novas matrÃƒÆ’Ã‚Â­culas para os cursos do pacote");

            // Atualiza contador de matrÃƒÆ’Ã‚Â­culas apenas para os cursos onde novas matrÃƒÆ’Ã‚Â­culas foram adicionadas
            $pdo->query("UPDATE cursos c
                JOIN temp_cursos_pacote tcp ON c.id = tcp.id_do_curso
                LEFT JOIN temp_matriculas_existentes tme ON tcp.id_do_curso = tme.id_curso
                SET c.matriculas = c.matriculas + 1
                WHERE tme.id_curso IS NULL");

            // Limpa as tabelas temporÃƒÆ’Ã‚Â¡rias
            $pdo->query("DROP TEMPORARY TABLE IF EXISTS temp_cursos_pacote");
            $pdo->query("DROP TEMPORARY TABLE IF EXISTS temp_matriculas_existentes");

            // Reativa o modo safe update
            $pdo->query("SET SQL_SAFE_UPDATES = 1");

            logMessage("LiberaÃƒÆ’Ã‚Â§ÃƒÆ’Ã‚Â£o automÃƒÆ’Ã‚Â¡tica de cursos do pacote concluÃƒÆ’Ã‚Â­da com sucesso");
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

// Libera automaticamente os cursos que pertencem a um profissionalizante (pacote)
function ativarCursosProfissionalizantes($pdo, $idProfissionalizante, $alunoId)
{
    try {
        $sqlCursos = $pdo->prepare("
            SELECT cp.id_curso, c.professor
              FROM cursos_profissionalizantes cp
              JOIN cursos c ON c.id = cp.id_curso
             WHERE cp.id_profissionalizante = :id_prof
        ");
        $sqlCursos->execute([':id_prof' => $idProfissionalizante]);
        $cursos = $sqlCursos->fetchAll(PDO::FETCH_ASSOC);

        if (!$cursos) {
            logMessage("Nenhum curso vinculado ao profissionalizante {$idProfissionalizante}");
            return false;
        }

        foreach ($cursos as $curso) {
            $idCurso = $curso['id_curso'];
            $professorId = $curso['professor'];

            // Se jÃƒÆ’Ã‚Â¡ existir matrÃƒÆ’Ã‚Â­cula do curso para o aluno, apenas garante status/liberaÃƒÆ’Ã‚Â§ÃƒÆ’Ã‚Â£o
            $stmtExiste = $pdo->prepare("SELECT id FROM matriculas_profissionalizantes WHERE aluno = :aluno AND id_curso = :curso AND pacote = 'NÃƒÆ’Ã‚Â£o' LIMIT 1");
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
                        (:curso, :aluno, :professor, 1, CURDATE(), 'Matriculado', 'NÃƒÆ’Ã‚Â£o', :id_pacote, 'Pacote', 'BOLETO')
                ");
                $stmtInsert->execute([
                    ':curso' => $idCurso,
                    ':aluno' => $alunoId,
                    ':professor' => $professorId,
                    ':id_pacote' => $idProfissionalizante
                ]);

                // Atualiza contador de matrÃƒÆ’Ã‚Â­culas do curso original
                $stmtContador = $pdo->prepare("UPDATE cursos SET matriculas = matriculas + 1 WHERE id = :curso");
                $stmtContador->execute([':curso' => $idCurso]);
            }
        }

        logMessage("LiberaÃƒÆ’Ã‚Â§ÃƒÆ’Ã‚Â£o de cursos vinculados ao profissionalizante {$idProfissionalizante} concluÃƒÆ’Ã‚Â­da");
        return true;
    } catch (PDOException $e) {
        logMessage("Erro ao liberar cursos do profissionalizante {$idProfissionalizante}: " . $e->getMessage());
        return false;
    }
}

// Processar webhook
try {


    // Verificar se ÃƒÆ’Ã‚Â© uma requisiÃƒÆ’Ã‚Â§ÃƒÆ’Ã‚Â£o POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['error' => 'MÃƒÆ’Ã‚Â©todo nÃƒÆ’Ã‚Â£o permitido']);
        exit;
    }

    // LÃƒÆ’Ã‚Âª o conteÃƒÆ’Ã‚Âºdo bruto enviado
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

    // Suporta JSON direto (charge_id/status) ou notification=hash
    $currentStatus = null;
    $chargeId = null;
    $lastItem = null;

    if (isset($data['charge_id']) && isset($data['status'])) {
        $chargeId = $data['charge_id'];
        $currentStatus = $data['status'];
        $lastItem = $data;
        logMessage("Webhook JSON direto recebido (parcelado). Charge ID: $chargeId, Status: $currentStatus");
    } else {
        // Extrai o notification hash
        $notification = trim($input);

        if (strpos($notification, '=') === false) {
            http_response_code(400);
            echo json_encode(['error' => 'Formato de notificaÃƒÆ’Ã‚Â§ÃƒÆ’Ã‚Â£o invÃƒÆ’Ã‚Â¡lido']);
            logWebhook($pdo, 'boleto_error', $payload, $receivedAt);
            exit;
        }

        list($key, $value) = explode('=', $notification, 2);
        $notification_hash = $value;
        $notification_hash = rtrim($notification_hash, '"');

        logMessage("Processando webhook de boleto parcelado. Notification hash: $notification_hash");

        // Consultar webhook da EFI
        $boletoWebhook = new EFIBoletoPayment(
            $config['client_id'],
            $config['client_secret'],
            $config['sandbox']
        );

        $result = $boletoWebhook->consultarWebhook($notification_hash);

        if (!$result || !isset($result['data']) || empty($result['data'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Dados do webhook invÃƒÆ’Ã‚Â¡lidos']);
            logWebhook($pdo, 'boleto_error', json_encode(['error' => 'Dados invÃƒÆ’Ã‚Â¡lidos', 'result' => $result]), $receivedAt);
            exit;
        }

        // Pega o ÃƒÆ’Ã‚Âºltimo item do array (status mais recente)
        $lastItem = end($result['data']);
        $currentStatus = $lastItem['status']['current'];
        $chargeId = $lastItem['identifiers']['charge_id'];
    }

    logMessage("Status atual do boleto: $currentStatus, Charge ID: $chargeId");

    // Atualiza tambÃƒÆ’Ã‚Â©m status/linha e recibo no registro da parcela para exibir ao aluno
    if ($chargeId) {
        $campos = ["situacao = :sit"];
        $params = [
            ':sit' => ($currentStatus === 'paid') ? 1 : 0,
            ':id' => $chargeId
        ];

        if (tabelaTemColuna($pdo, 'parcelas_geradas_por_boleto', 'status')) {
            $campos[] = "status = :status";
            $params[':status'] = $currentStatus;
        }

        if ($currentStatus === 'paid' && tabelaTemColuna($pdo, 'parcelas_geradas_por_boleto', 'data_pagamento')) {
            $campos[] = "data_pagamento = COALESCE(data_pagamento, NOW())";
        }

        $recibo = $lastItem['status']['details']['receipt'] ?? ($lastItem['transactionReceiptUrl'] ?? null);
        if (is_string($recibo) && trim($recibo) !== '' && stripos($recibo, 'http') === 0) {
            $campos[] = "transaction_receipt_url = :recibo";
            $params[':recibo'] = $recibo;
        }

        $sql = "UPDATE parcelas_geradas_por_boleto SET " . implode(', ', $campos) . " WHERE charge_id = :id OR id_asaas = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
    }

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
                        throw new Exception('Falha ao atualizar matrÃƒÆ’Ã‚Â­cula');
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
                    error_log("MatrÃƒÆ’Ã‚Â­cula nÃƒÆ’Ã‚Â£o localizada em nenhuma tabela para Charge ID: $chargeId");
                }
            } else {
                error_log("ID da matrÃƒÆ’Ã‚Â­cula nÃƒÆ’Ã‚Â£o encontrado para Charge ID: $chargeId");
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



