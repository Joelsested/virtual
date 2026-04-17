<?php
require_once('../conexao.php');
require_once(__DIR__ . '/../../config/session.php');
sested_session_start();

function responderErroEntrarComoAluno(string $mensagem): void
{
    $msg = addslashes($mensagem);
    echo "<script>alert('{$msg}');window.location.href='index.php?pagina=alunos';</script>";
    exit();
}

function garantirTabelaAuditoriaImpersonacao(PDO $pdo): void
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

function registrarAuditoriaImpersonacao(PDO $pdo, int $adminId, int $alunoUsuarioId, int $alunoIdPessoa): void
{
    if ($adminId <= 0 || $alunoUsuarioId <= 0) {
        return;
    }

    try {
        garantirTabelaAuditoriaImpersonacao($pdo);
        $ip = (string) ($_SERVER['REMOTE_ADDR'] ?? '');
        $ua = substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255);

        $stmt = $pdo->prepare("
            INSERT INTO auditoria_impersonacao
                (usuario_admin_id, usuario_aluno_id, aluno_id_pessoa, ip, user_agent, acao)
            VALUES
                (:admin, :aluno_usuario, :aluno_pessoa, :ip, :ua, 'ENTRAR_COMO_ALUNO')
        ");
        $stmt->execute([
            ':admin' => $adminId,
            ':aluno_usuario' => $alunoUsuarioId,
            ':aluno_pessoa' => $alunoIdPessoa,
            ':ip' => $ip,
            ':ua' => $ua,
        ]);
    } catch (Throwable $e) {
        // Nao bloqueia o fluxo em caso de falha de auditoria.
    }
}

$usuarioSessao = (int) ($_SESSION['id'] ?? 0);
$nivelSessao = (string) ($_SESSION['nivel'] ?? '');

if ($usuarioSessao <= 0 || $nivelSessao === '') {
    responderErroEntrarComoAluno('Sessao invalida. Faca login novamente.');
}

$niveisPermitidos = ['Administrador', 'Secretario', 'Tesoureiro', 'Vendedor', 'Tutor', 'Parceiro', 'Assessor', 'Professor'];
if (!in_array($nivelSessao, $niveisPermitidos, true)) {
    responderErroEntrarComoAluno('Permissao negada para entrar como aluno.');
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    responderErroEntrarComoAluno('Requisicao invalida.');
}

$alunoIdPessoa = (int) ($_POST['aluno_id'] ?? 0);
if ($alunoIdPessoa <= 0) {
    responderErroEntrarComoAluno('Aluno invalido.');
}

$stmtAluno = $pdo->prepare("SELECT id, usuario, nome FROM alunos WHERE id = :id LIMIT 1");
$stmtAluno->execute([':id' => $alunoIdPessoa]);
$alunoCadastro = $stmtAluno->fetch(PDO::FETCH_ASSOC) ?: [];
if (!$alunoCadastro) {
    responderErroEntrarComoAluno('Aluno nao encontrado.');
}

$usuarioResponsavelAluno = (int) ($alunoCadastro['usuario'] ?? 0);
$niveisComAcessoGlobal = ['Administrador', 'Secretario', 'Tesoureiro'];
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
$stmtAlunoUsuario->execute([':id_pessoa' => $alunoIdPessoa]);
$alunoUsuario = $stmtAlunoUsuario->fetch(PDO::FETCH_ASSOC) ?: [];

if (!$alunoUsuario) {
    responderErroEntrarComoAluno('Não existe usuário de acesso para este aluno.');
}

if ((string) ($alunoUsuario['ativo'] ?? '') !== 'Sim') {
    responderErroEntrarComoAluno('Usuario do aluno esta inativo.');
}

$_SESSION['switch_back_id'] = $usuarioSessao;
$_SESSION['switch_back_nivel'] = $nivelSessao;
$_SESSION['switch_back_nome'] = (string) ($_SESSION['nome'] ?? '');
$_SESSION['switch_back_cpf'] = (string) ($_SESSION['cpf'] ?? '');
unset($_SESSION['switch_vendedor_usuario_id']);

$_SESSION['id'] = (int) ($alunoUsuario['id'] ?? 0);
$_SESSION['nivel'] = 'Aluno';
$_SESSION['nome'] = (string) ($alunoUsuario['nome'] ?? '');
$_SESSION['cpf'] = (string) ($alunoUsuario['cpf'] ?? '');

registrarAuditoriaImpersonacao(
    $pdo,
    $usuarioSessao,
    (int) ($_SESSION['id'] ?? 0),
    (int) ($alunoUsuario['id_pessoa'] ?? 0)
);

$idDestino = (int) ($_SESSION['id'] ?? 0);
$nivelDestino = (string) ($_SESSION['nivel'] ?? '');

echo "<script>
try {
    localStorage.setItem('active_user_id', '{$idDestino}');
    localStorage.setItem('active_user_level', '{$nivelDestino}');
    localStorage.setItem('active_user_at', String(Date.now()));
} catch (e) {}
window.location.href = '../painel-aluno/index.php?pagina=home';
</script>";
exit();
