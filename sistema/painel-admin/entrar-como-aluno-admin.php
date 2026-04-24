<?php
require_once('../conexao.php');
require_once(__DIR__ . '/../../config/session.php');
sested_session_start();

function responderErroEntrarComoAluno($mensagem)
{
    $msg = addslashes((string) $mensagem);
    echo "<script>alert('{$msg}');window.location.href='index.php?pagina=alunos';</script>";
    exit();
}

function garantirTabelaAuditoriaImpersonacao($pdo)
{
    $sql = "
        CREATE TABLE IF NOT EXISTS auditoria_impersonacao (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_admin_id INT NOT NULL,
            usuario_aluno_id INT NOT NULL,
            aluno_id_pessoa INT NOT NULL DEFAULT 0,
            ip VARCHAR(45) NOT NULL DEFAULT '',
            user_agent VARCHAR(255) NOT NULL DEFAULT '',
            data_hora DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            acao VARCHAR(40) NOT NULL DEFAULT 'ENTRAR_COMO_ALUNO',
            INDEX idx_admin (usuario_admin_id),
            INDEX idx_aluno (usuario_aluno_id),
            INDEX idx_data_hora (data_hora)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";
    $pdo->exec($sql);
}

function registrarAuditoriaImpersonacao($pdo, $adminId, $alunoUsuarioId, $alunoIdPessoa)
{
    $adminId = (int) $adminId;
    $alunoUsuarioId = (int) $alunoUsuarioId;
    $alunoIdPessoa = (int) $alunoIdPessoa;

    if ($adminId <= 0 || $alunoUsuarioId <= 0) {
        return;
    }

    try {
        garantirTabelaAuditoriaImpersonacao($pdo);
        $ip = isset($_SERVER['REMOTE_ADDR']) ? (string) $_SERVER['REMOTE_ADDR'] : '';
        $uaRaw = isset($_SERVER['HTTP_USER_AGENT']) ? (string) $_SERVER['HTTP_USER_AGENT'] : '';
        $ua = substr($uaRaw, 0, 255);

        $stmt = $pdo->prepare("
            INSERT INTO auditoria_impersonacao
                (usuario_admin_id, usuario_aluno_id, aluno_id_pessoa, ip, user_agent, acao)
            VALUES
                (:admin, :aluno_usuario, :aluno_pessoa, :ip, :ua, 'ENTRAR_COMO_ALUNO')
        ");
        $stmt->execute(array(
            ':admin' => $adminId,
            ':aluno_usuario' => $alunoUsuarioId,
            ':aluno_pessoa' => $alunoIdPessoa,
            ':ip' => $ip,
            ':ua' => $ua,
        ));
    } catch (Exception $e) {
        // Nao bloqueia o fluxo em caso de falha de auditoria.
    }
}

$usuarioSessao = isset($_SESSION['id']) ? (int) $_SESSION['id'] : 0;
$nivelSessao = isset($_SESSION['nivel']) ? (string) $_SESSION['nivel'] : '';

if ($usuarioSessao <= 0 || $nivelSessao === '') {
    responderErroEntrarComoAluno('Sessao invalida. Faca login novamente.');
}

$niveisPermitidos = array('Administrador', 'Secretario', 'Tesoureiro', 'Vendedor', 'Tutor', 'Parceiro', 'Assessor', 'Professor');
if (!in_array($nivelSessao, $niveisPermitidos, true)) {
    responderErroEntrarComoAluno('Permissao negada para entrar como aluno.');
}

$method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '';
if ($method !== 'POST') {
    responderErroEntrarComoAluno('Requisicao invalida.');
}

$alunoIdPessoa = isset($_POST['aluno_id']) ? (int) $_POST['aluno_id'] : 0;
if ($alunoIdPessoa <= 0) {
    responderErroEntrarComoAluno('Aluno invalido.');
}

$stmtAluno = $pdo->prepare("SELECT id, usuario, nome FROM alunos WHERE id = :id LIMIT 1");
$stmtAluno->execute(array(':id' => $alunoIdPessoa));
$alunoCadastro = $stmtAluno->fetch(PDO::FETCH_ASSOC);
if (!$alunoCadastro) {
    responderErroEntrarComoAluno('Aluno nao encontrado.');
}

$usuarioResponsavelAluno = isset($alunoCadastro['usuario']) ? (int) $alunoCadastro['usuario'] : 0;
$niveisComAcessoGlobal = array('Administrador', 'Secretario', 'Tesoureiro');
$temAcessoGlobal = in_array($nivelSessao, $niveisComAcessoGlobal, true);
if (!$temAcessoGlobal && $usuarioResponsavelAluno !== $usuarioSessao) {
    responderErroEntrarComoAluno('Permissao negada. Voce pode entrar apenas nos seus alunos.');
}

$stmtAlunoUsuario = $pdo->prepare("
    SELECT id, nome, cpf, nivel, ativo, id_pessoa
    FROM usuarios
    WHERE id_pessoa = :id_pessoa
      AND nivel = 'Aluno'
    ORDER BY (ativo = 'Sim') DESC, id DESC
    LIMIT 1
");
$stmtAlunoUsuario->execute(array(':id_pessoa' => $alunoIdPessoa));
$alunoUsuario = $stmtAlunoUsuario->fetch(PDO::FETCH_ASSOC);

if (!$alunoUsuario) {
    responderErroEntrarComoAluno('Nao existe usuario de acesso para este aluno.');
}

$alunoAtivo = isset($alunoUsuario['ativo']) ? (string) $alunoUsuario['ativo'] : '';
if ($alunoAtivo !== 'Sim') {
    responderErroEntrarComoAluno('Usuario do aluno esta inativo.');
}

$_SESSION['switch_back_id'] = $usuarioSessao;
$_SESSION['switch_back_nivel'] = $nivelSessao;
$_SESSION['switch_back_nome'] = isset($_SESSION['nome']) ? (string) $_SESSION['nome'] : '';
$_SESSION['switch_back_cpf'] = isset($_SESSION['cpf']) ? (string) $_SESSION['cpf'] : '';
unset($_SESSION['switch_vendedor_usuario_id']);

$_SESSION['id'] = isset($alunoUsuario['id']) ? (int) $alunoUsuario['id'] : 0;
$_SESSION['nivel'] = 'Aluno';
$_SESSION['nome'] = isset($alunoUsuario['nome']) ? (string) $alunoUsuario['nome'] : '';
$_SESSION['cpf'] = isset($alunoUsuario['cpf']) ? (string) $alunoUsuario['cpf'] : '';

registrarAuditoriaImpersonacao(
    $pdo,
    $usuarioSessao,
    isset($_SESSION['id']) ? (int) $_SESSION['id'] : 0,
    isset($alunoUsuario['id_pessoa']) ? (int) $alunoUsuario['id_pessoa'] : 0
);

$idDestino = isset($_SESSION['id']) ? (int) $_SESSION['id'] : 0;
$nivelDestino = isset($_SESSION['nivel']) ? (string) $_SESSION['nivel'] : '';

echo "<script>
try {
    localStorage.setItem('active_user_id', '{$idDestino}');
    localStorage.setItem('active_user_level', '{$nivelDestino}');
    localStorage.setItem('active_user_at', String(Date.now()));
} catch (e) {}
window.location.href = '../painel-aluno/index.php?pagina=home';
</script>";
exit();
