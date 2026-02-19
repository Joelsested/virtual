<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../sistema/conexao.php';
require_once __DIR__ . '/pix.php';

$options = require_once __DIR__ . '/options.php';

function localizarIdMatriculaPorTxid(PDO $pdo, string $txid): ?int
{
    $stmt = $pdo->prepare("SELECT id_matricula FROM pagamentos_pix WHERE txid = :txid LIMIT 1");
    $stmt->execute([':txid' => $txid]);
    $id = $stmt->fetchColumn();
    return $id ? (int) $id : null;
}

function ativarCursosDoPacote(PDO $pdo, int $idCurso, int $alunoId): bool
{
    try {
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

        $query_count = $pdo->query("SELECT COUNT(*) FROM temp_cursos_pacote");
        $total_registros = (int) $query_count->fetchColumn();

        if ($total_registros <= 0) {
            $pdo->query("DROP TEMPORARY TABLE IF EXISTS temp_cursos_pacote");
            return false;
        }

        $pdo->query("CREATE TEMPORARY TABLE temp_matriculas_existentes AS
            SELECT id_curso
            FROM matriculas
            WHERE aluno = {$alunoId} AND id_pacote = {$idCurso}");

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

        $pdo->query("UPDATE cursos c
            JOIN temp_cursos_pacote tcp ON c.id = tcp.id_do_curso
            LEFT JOIN temp_matriculas_existentes tme ON tcp.id_do_curso = tme.id_curso
            SET c.matriculas = c.matriculas + 1
            WHERE tme.id_curso IS NULL");

        $pdo->query("DROP TEMPORARY TABLE IF EXISTS temp_cursos_pacote");
        $pdo->query("DROP TEMPORARY TABLE IF EXISTS temp_matriculas_existentes");
        return true;
    } catch (Exception $e) {
        error_log('[verificar_pix] erro ao ativar pacote: ' . $e->getMessage());
        return false;
    }
}

$txid = isset($_GET['id_pagamento']) ? trim((string) $_GET['id_pagamento']) : '';
if ($txid === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Txid ausente']);
    exit;
}

try {
    $pix = new EFIPixPayment(
        $options['clientId'],
        $options['clientSecret'],
        $options['certificate'],
        $options['sandbox']
    );
    $transacao = $pix->consultarCobranca($txid);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Falha ao consultar pagamento', 'details' => $e->getMessage()]);
    exit;
}

$status = $transacao['status'] ?? '';
$statusResposta = ($status === 'CONCLUIDA') ? 'aprovado' : strtolower((string) $status);
$updated = false;
$idMatricula = null;

if ($status === 'CONCLUIDA') {
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("UPDATE pagamentos_pix SET status = 'CONCLUIDA' WHERE txid = :txid");
        $stmt->execute([':txid' => $txid]);

        $idMatricula = localizarIdMatriculaPorTxid($pdo, $txid);
        if ($idMatricula) {
            $stmt = $pdo->prepare("UPDATE matriculas SET status = 'Matriculado', forma_pgto = 'PIX' WHERE id = :id");
            $stmt->execute([':id' => $idMatricula]);

            $stmt = $pdo->prepare("SELECT id_curso, aluno, pacote FROM matriculas WHERE id = :id");
            $stmt->execute([':id' => $idMatricula]);
            $dadosMatricula = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($dadosMatricula && isset($dadosMatricula['pacote']) && strcasecmp($dadosMatricula['pacote'], 'Sim') === 0) {
                ativarCursosDoPacote($pdo, (int) $dadosMatricula['id_curso'], (int) $dadosMatricula['aluno']);
            }
        }

        $pdo->commit();
        $updated = true;
    } catch (Exception $e) {
        $pdo->rollBack();
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Falha ao atualizar matricula', 'details' => $e->getMessage()]);
        exit;
    }
}

echo json_encode([
    'success' => true,
    'status' => $statusResposta,
    'efi_status' => $status,
    'updated' => $updated,
    'id_matricula' => $idMatricula
]);
