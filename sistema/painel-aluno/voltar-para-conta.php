<?php
require_once('../conexao.php');
require_once __DIR__ . '/../../config/session.php';
sested_session_start();

function sairComMensagemAluno(string $mensagem): void
{
    $mensagem = addslashes($mensagem);
    echo "<script>alert('{$mensagem}');window.location.href='index.php';</script>";
    exit();
}

$idRetorno = (int) ($_SESSION['switch_back_id'] ?? 0);
$nivelRetorno = (string) ($_SESSION['switch_back_nivel'] ?? '');

if ($idRetorno <= 0 || $nivelRetorno === '') {
    sairComMensagemAluno('Não existe conta de retorno disponível nesta sessão.');
}

$stmt = $pdo->prepare("SELECT id, nome, cpf, nivel, ativo FROM usuarios WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $idRetorno]);
$contaRetorno = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

if (!$contaRetorno || ($contaRetorno['ativo'] ?? '') !== 'Sim') {
    unset(
        $_SESSION['switch_back_id'],
        $_SESSION['switch_back_nivel'],
        $_SESSION['switch_back_nome'],
        $_SESSION['switch_back_cpf'],
        $_SESSION['switch_origem_usuario_id'],
        $_SESSION['switch_origem_nivel'],
        $_SESSION['switch_vendedor_usuario_id']
    );
    sairComMensagemAluno('Conta de retorno invalida ou inativa.');
}

$_SESSION['id'] = (int) $contaRetorno['id'];
$_SESSION['nivel'] = (string) $contaRetorno['nivel'];
$_SESSION['nome'] = (string) ($contaRetorno['nome'] ?? '');
$_SESSION['cpf'] = (string) ($contaRetorno['cpf'] ?? '');

unset(
    $_SESSION['switch_back_id'],
    $_SESSION['switch_back_nivel'],
    $_SESSION['switch_back_nome'],
    $_SESSION['switch_back_cpf'],
    $_SESSION['switch_origem_usuario_id'],
    $_SESSION['switch_origem_nivel'],
    $_SESSION['switch_vendedor_usuario_id']
);

$destino = ($_SESSION['nivel'] === 'Aluno') ? 'index.php?pagina=home' : '../painel-admin/index.php';
echo "<script>
try {
    localStorage.setItem('active_user_id', '" . (int) $_SESSION['id'] . "');
    localStorage.setItem('active_user_level', '" . addslashes((string) $_SESSION['nivel']) . "');
    localStorage.setItem('active_user_at', String(Date.now()));
} catch (e) {}
window.location.href = '" . addslashes($destino) . "';
</script>";
exit();

?>
