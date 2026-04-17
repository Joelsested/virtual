<?php
require_once('../conexao.php');
require_once('verificar.php');
$pag = 'pagamentos_aluno';

@session_start();

$nivel = $_SESSION['nivel'] ?? '';
if (!in_array($nivel, ['Administrador', 'Secretario', 'Tesoureiro', 'Tutor', 'Parceiro', 'Professor', 'Vendedor'], true)) {
    echo "<script>window.location='../index.php'</script>";
    exit();
}

$alunoPessoaId = filter_input(INPUT_GET, 'aluno', FILTER_VALIDATE_INT);
if (!$alunoPessoaId) {
    echo "<script>window.location='index.php?pagina=alunos'</script>";
    exit();
}

$stmtAluno = $pdo->prepare('SELECT id, nome, email FROM alunos WHERE id = :id LIMIT 1');
$stmtAluno->execute([':id' => $alunoPessoaId]);
$alunoRow = $stmtAluno->fetch(PDO::FETCH_ASSOC);
if (!$alunoRow) {
    echo '<div class="alert alert-danger">Aluno não encontrado.</div>';
    exit();
}

$stmtUsuario = $pdo->prepare("SELECT id, nome FROM usuarios WHERE id_pessoa = :id_pessoa AND nivel = 'Aluno' LIMIT 1");
$stmtUsuario->execute([':id_pessoa' => $alunoPessoaId]);
$usuarioAluno = $stmtUsuario->fetch(PDO::FETCH_ASSOC);
if (!$usuarioAluno) {
    $stmtUsuarioEmail = $pdo->prepare("SELECT id, nome FROM usuarios WHERE usuario = :email AND nivel = 'Aluno' LIMIT 1");
    $stmtUsuarioEmail->execute([':email' => (string) ($alunoRow['email'] ?? '')]);
    $usuarioAluno = $stmtUsuarioEmail->fetch(PDO::FETCH_ASSOC);
}

if (!$usuarioAluno) {
    echo '<div class="alert alert-warning">Usuário do aluno não localizado para exibir pagamentos.</div>';
    exit();
}

$idAlunoUsuario = (int) $usuarioAluno['id'];
$nomeAluno = $usuarioAluno['nome'] ?? ($alunoRow['nome'] ?? 'Aluno');
$aba = $_GET['aba'] ?? 'parcelas';
if (!in_array($aba, ['parcelas', 'parcelas_cartao'], true)) {
    $aba = 'parcelas';
}

$arquivoPainelAluno = $aba === 'parcelas_cartao' ? 'parcelas_cartao.php' : 'parcelas.php';

$cwdOriginal = getcwd();
$basePainelAluno = dirname(__DIR__) . '/../painel-aluno';
$oldAlunoId = $_GET['aluno_id'] ?? null;
$oldAdminView = $_GET['admin_view'] ?? null;

$_GET['aluno_id'] = $idAlunoUsuario;
$_GET['admin_view'] = '1';

?>

<div class="bs-example widget-shadow" style="padding:15px">
    <h3>PAGAMENTOS DO ALUNO: <b><?php echo htmlspecialchars($nomeAluno, ENT_QUOTES, 'UTF-8'); ?></b></h3>

    <div style="margin: 12px 0 18px 0; display:flex; gap:8px; flex-wrap:wrap;">
        <a class="btn btn-<?php echo $aba === 'parcelas' ? 'primary' : 'default'; ?>"
           href="index.php?pagina=pagamentos_aluno&aluno=<?php echo (int) $alunoPessoaId; ?>&aba=parcelas">
            Parcelas Boleto
        </a>
        <a class="btn btn-<?php echo $aba === 'parcelas_cartao' ? 'primary' : 'default'; ?>"
           href="index.php?pagina=pagamentos_aluno&aluno=<?php echo (int) $alunoPessoaId; ?>&aba=parcelas_cartao">
            Parcelas Cartão
        </a>
    </div>

    <?php
    if (!is_dir($basePainelAluno)) {
        echo '<div class="alert alert-danger">Diretório do painel do aluno não encontrado.</div>';
    } else {
        chdir($basePainelAluno);
        $arquivo = 'paginas/' . $arquivoPainelAluno;
        if (is_file($arquivo)) {
            include $arquivo;
        } else {
            echo '<div class="alert alert-danger">Tela de pagamentos do aluno não encontrada.</div>';
        }
    }

    if ($cwdOriginal) {
        chdir($cwdOriginal);
    }

    if ($oldAlunoId === null) {
        unset($_GET['aluno_id']);
    } else {
        $_GET['aluno_id'] = $oldAlunoId;
    }

    if ($oldAdminView === null) {
        unset($_GET['admin_view']);
    } else {
        $_GET['admin_view'] = $oldAdminView;
    }
    ?>
</div>
