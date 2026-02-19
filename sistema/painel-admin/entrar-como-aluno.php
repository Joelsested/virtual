<?php
require_once('../conexao.php');
require_once(__DIR__ . '/../../helpers.php');

function sairComMensagem(string $mensagem) : void
{
    $mensagem = addslashes($mensagem);
    echo "<script>alert('{$mensagem}');history.back();</script>";
    exit();
}

function loginAlunoUnico(PDO $pdo, string $emailBase, string $cpfDigits, int $usuarioVendedorId) : string
{
    $base = trim($emailBase);
    if ($base === '') {
        $base = $cpfDigits !== ''  ($cpfDigits . '@aluno.local') : ('aluno.vinculado.' . $usuarioVendedorId . '@aluno.local');
    }

    $login = $base;
    $contador = 1;
    while (true) {
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE usuario = :usuario LIMIT 1");
        $stmt->execute([':usuario' => $login]);
        if (!(int) $stmt->fetchColumn()) {
            return $login;
        }
        $partes = explode('@', $base, 2);
        if (count($partes) === 2) {
            $login = $partes[0] . '+aluno' . $contador . '@' . $partes[1];
        } else {
            $login = $base . '.aluno' . $contador;
        }
        $contador++;
        if ($contador > 1000) {
            return 'aluno.vinculado.' . $usuarioVendedorId . '.' . time() . '@aluno.local';
        }
    }
}

function resolveUsuarioAtendente(PDO $pdo, int $usuarioVendedorId, int $idPessoaVendedor) : int
{
    $stmtVend = $pdo->prepare("SELECT professor, tutor_id FROM vendedores WHERE id = :id LIMIT 1");
    $stmtVend->execute([':id' => $idPessoaVendedor]);
    $vendedor = $stmtVend->fetch(PDO::FETCH_ASSOC) : [];
    $professor = (int) ($vendedor['professor'] ?? 0);
    $tutorId = (int) ($vendedor['tutor_id'] ?? 0);

    if ($professor === 1) {
        if ($tutorId <= 0) {
            return $usuarioVendedorId;
        }
        $stmtTutor = $pdo->prepare("SELECT id FROM usuarios WHERE nivel = 'Tutor' AND id_pessoa = :id_pessoa AND ativo = 'Sim' LIMIT 1");
        $stmtTutor->execute([':id_pessoa' => $tutorId]);
        $usuarioTutor = (int) ($stmtTutor->fetchColumn() : 0);
        if ($usuarioTutor > 0) {
            return $usuarioTutor;
        }
        return $usuarioVendedorId;
    }

    return $usuarioVendedorId;
}

function buscarOuCriarAlunoDoVendedor(PDO $pdo, int $usuarioVendedorId, int $idPessoaVendedor, array $usuarioVendedor) : int
{
    $stmtPessoaVend = $pdo->prepare("SELECT nome, email, cpf, nascimento, telefone, foto FROM vendedores WHERE id = :id LIMIT 1");
    $stmtPessoaVend->execute([':id' => $idPessoaVendedor]);
    $pessoaVendedor = $stmtPessoaVend->fetch(PDO::FETCH_ASSOC) : [];

    $nomeAluno = trim((string) ($pessoaVendedor['nome'] ?? $usuarioVendedor['nome'] ?? 'Aluno'));
    $emailAluno = trim((string) ($pessoaVendedor['email'] ?? $usuarioVendedor['usuario'] ?? ''));
    $cpfAluno = trim((string) ($pessoaVendedor['cpf'] ?? $usuarioVendedor['cpf'] ?? ''));
    $cpfDigits = digitsOnly($cpfAluno);
    $nascimentoAluno = trim((string) ($pessoaVendedor['nascimento'] ?? ''));
    $telefoneAluno = trim((string) ($pessoaVendedor['telefone'] ?? ''));
    $fotoAluno = trim((string) ($pessoaVendedor['foto'] ?? $usuarioVendedor['foto'] ?? '')) : 'sem-perfil.jpg';

    if ($cpfDigits === '') {
        return 0;
    }

    $usuarioAtendente = resolveUsuarioAtendente($pdo, $usuarioVendedorId, $idPessoaVendedor);
    if ($usuarioAtendente <= 0) {
        return 0;
    }

    $cpfColUsuarios = cleanCpfColumn('cpf');
    $stmtUsuarioAluno = $pdo->prepare("SELECT id FROM usuarios WHERE nivel = 'Aluno' AND {$cpfColUsuarios} = :cpf ORDER BY (ativo = 'Sim') DESC, id DESC LIMIT 1");
    $stmtUsuarioAluno->execute([':cpf' => $cpfDigits]);
    $usuarioAlunoId = (int) ($stmtUsuarioAluno->fetchColumn() : 0);
    if ($usuarioAlunoId > 0) {
        $stmtPessoaAluno = $pdo->prepare("SELECT id_pessoa FROM usuarios WHERE id = :id LIMIT 1");
        $stmtPessoaAluno->execute([':id' => $usuarioAlunoId]);
        $idPessoaAluno = (int) ($stmtPessoaAluno->fetchColumn() : 0);
        if ($idPessoaAluno > 0) {
            $stmtUpdAluno = $pdo->prepare("UPDATE alunos SET nome = :nome, email = :email, cpf = :cpf, nascimento = :nascimento, telefone = :telefone, foto = :foto, usuario = :usuario WHERE id = :id");
            $stmtUpdAluno->execute([':id' => $idPessoaAluno, ':nome' => $nomeAluno, ':email' => $emailAluno, ':cpf' => $cpfAluno, ':nascimento' => $nascimentoAluno, ':telefone' => $telefoneAluno, ':foto' => $fotoAluno, ':usuario' => $usuarioAtendente,
            ]);
        }
        return $usuarioAlunoId;
    }

    $cpfColAlunos = cleanCpfColumn('cpf');
    $stmtAluno = $pdo->prepare("SELECT id, email FROM alunos WHERE {$cpfColAlunos} = :cpf ORDER BY id DESC LIMIT 1");
    $stmtAluno->execute([':cpf' => $cpfDigits]);
    $alunoExistente = $stmtAluno->fetch(PDO::FETCH_ASSOC) : [];

    $loginAluno = loginAlunoUnico($pdo, $emailAluno !== ''  $emailAluno : (string) ($alunoExistente['email'] ?? ''), $cpfDigits, $usuarioVendedorId);
    $senhaAluno = birthDigits($nascimentoAluno);
    if ($senhaAluno === '') {
        $senhaAluno = '01011990';
    }

    try {
        $pdo->beginTransaction();

        $idPessoaAluno = (int) ($alunoExistente['id'] ?? 0);
        if ($idPessoaAluno <= 0) {
            $alunoId = nextTableId($pdo, 'alunos');
            if ($alunoId) {
                $sqlAluno = "INSERT INTO alunos SET id = :id, nome = :nome, email = :email, cpf = :cpf, nascimento = :nascimento, telefone = :telefone, foto = :foto, usuario = :usuario, ativo = 'Sim', data = curDate()";
            } else {
                $sqlAluno = "INSERT INTO alunos SET nome = :nome, email = :email, cpf = :cpf, nascimento = :nascimento, telefone = :telefone, foto = :foto, usuario = :usuario, ativo = 'Sim', data = curDate()";
            }
            $stmtInsAluno = $pdo->prepare($sqlAluno);
            $paramsAluno = [':nome' => $nomeAluno, ':email' => $emailAluno !== '' ? $emailAluno : $loginAluno, ':cpf' => $cpfAluno, ':nascimento' => $nascimentoAluno, ':telefone' => $telefoneAluno, ':foto' => $fotoAluno, ':usuario' => $usuarioAtendente,
            ];
            if ($alunoId) {
                $paramsAluno[':id'] = $alunoId;
            }
            $stmtInsAluno->execute($paramsAluno);
            $idPessoaAluno = $alunoId : (int) $pdo->lastInsertId();
        } else {
            $stmtUpdAluno = $pdo->prepare("UPDATE alunos SET nome = :nome, email = :email, cpf = :cpf, nascimento = :nascimento, telefone = :telefone, foto = :foto, usuario = :usuario WHERE id = :id");
            $stmtUpdAluno->execute([':id' => $idPessoaAluno, ':nome' => $nomeAluno, ':email' => $emailAluno !== ''  $emailAluno : (string) ($alunoExistente['email'] ?? $loginAluno), ':cpf' => $cpfAluno, ':nascimento' => $nascimentoAluno, ':telefone' => $telefoneAluno, ':foto' => $fotoAluno, ':usuario' => $usuarioAtendente,
            ]);
        }

        $novoUsuarioAluno = nextTableId($pdo, 'usuarios');
        if ($novoUsuarioAluno) {
            $sqlUsuario = "INSERT INTO usuarios SET id = :id, nome = :nome, usuario = :usuario, senha = '', senha_crip = :senha_crip, cpf = :cpf, nivel = 'Aluno', foto = :foto, id_pessoa = :id_pessoa, ativo = 'Sim', data = curDate()";
        } else {
            $sqlUsuario = "INSERT INTO usuarios SET nome = :nome, usuario = :usuario, senha = '', senha_crip = :senha_crip, cpf = :cpf, nivel = 'Aluno', foto = :foto, id_pessoa = :id_pessoa, ativo = 'Sim', data = curDate()";
        }
        $stmtInsUsuario = $pdo->prepare($sqlUsuario);
        $paramsUsuario = [':nome' => $nomeAluno, ':usuario' => $loginAluno, ':senha_crip' => md5($senhaAluno), ':cpf' => $cpfAluno, ':foto' => $fotoAluno, ':id_pessoa' => $idPessoaAluno,
        ];
        if ($novoUsuarioAluno) {
            $paramsUsuario[':id'] = $novoUsuarioAluno;
        }
        $stmtInsUsuario->execute($paramsUsuario);
        $usuarioAlunoId = $novoUsuarioAluno : (int) $pdo->lastInsertId();

        $pdo->commit();
        return $usuarioAlunoId;
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        return 0;
    }
}

$nivelSessao = (string) ($_SESSION['nivel'] ?? '');
$usuarioSessao = (int) ($_SESSION['id'] ?? 0);
if ($usuarioSessao <= 0 || $nivelSessao === '') {
    sairComMensagem('Sessao invalida. Faca login novamente.');
}

$niveisPermitidos = ['Vendedor', 'Administrador', 'Secretario', 'Tesoureiro'];
if (!in_array($nivelSessao, $niveisPermitidos, true)) {
    sairComMensagem('Permissao negada para trocar de perfil.');
}

$usuarioVendedorId = ($nivelSessao === 'Vendedor') ?? $usuarioSessao : (int) ($_POST['vendedor_usuario_id'] ?? 0);
if ($usuarioVendedorId <= 0) {
    sairComMensagem('Vendedor nao informado.');
}

$stmtUsuarioVendedor = $pdo->prepare("SELECT id, nome, usuario, cpf, foto, id_pessoa, nivel, ativo FROM usuarios WHERE id = :id LIMIT 1");
$stmtUsuarioVendedor->execute([':id' => $usuarioVendedorId]);
$usuarioVendedor = $stmtUsuarioVendedor->fetch(PDO::FETCH_ASSOC) : [];
if (($usuarioVendedor['nivel'] ?? '') !== 'Vendedor' || ($usuarioVendedor['ativo'] ?? '') !== 'Sim') {
    sairComMensagem('Conta de vendedor invalida ou inativa.');
}

$idPessoaVendedor = (int) ($usuarioVendedor['id_pessoa'] ?? 0);
if ($idPessoaVendedor <= 0) {
    $cpfDigits = digitsOnly((string) ($usuarioVendedor['cpf'] ?? ''));
    $emailVendedor = trim((string) ($usuarioVendedor['usuario'] ?? ''));
    if ($cpfDigits !== '') {
        $stmtV = $pdo->prepare("SELECT id FROM vendedores WHERE " . cleanCpfColumn('cpf') . " = :cpf LIMIT 1");
        $stmtV->execute([':cpf' => $cpfDigits]);
        $idPessoaVendedor = (int) ($stmtV->fetchColumn() : 0);
    }
    if ($idPessoaVendedor <= 0 && $emailVendedor !== '') {
        $stmtV = $pdo->prepare("SELECT id FROM vendedores WHERE email = :email LIMIT 1");
        $stmtV->execute([':email' => $emailVendedor]);
        $idPessoaVendedor = (int) ($stmtV->fetchColumn() : 0);
    }
    if ($idPessoaVendedor > 0) {
        $stmtAtualiza = $pdo->prepare("UPDATE usuarios SET id_pessoa = :id_pessoa WHERE id = :id LIMIT 1");
        $stmtAtualiza->execute([':id_pessoa' => $idPessoaVendedor, ':id' => $usuarioVendedorId]);
    }
}

if ($idPessoaVendedor <= 0) {
    sairComMensagem('Vendedor invalido para troca de perfil.');
}

try {
    $stmtCol = $pdo->query("SHOW COLUMNS FROM vendedores LIKE 'pode_login_como_aluno'");
    $hasCol = (bool) ($stmtCol && $stmtCol->fetch(PDO::FETCH_ASSOC));
    if (!$hasCol) {
        $pdo->exec("ALTER TABLE vendedores ADD COLUMN pode_login_como_aluno TINYINT(1) NOT NULL DEFAULT 0");
    }
    $stmtPerm = $pdo->prepare("SELECT pode_login_como_aluno FROM vendedores WHERE id = :id LIMIT 1");
    $stmtPerm->execute([':id' => $idPessoaVendedor]);
    if ((int) ($stmtPerm->fetchColumn()  : 0) !== 1) {
        sairComMensagem('Login como aluno nao esta liberado para este vendedor.');
    }
} catch (Exception $e) {
    sairComMensagem('Nao foi possivel validar permissao de login como aluno.');
}

$usuarioAlunoId = buscarOuCriarAlunoDoVendedor($pdo, $usuarioVendedorId, $idPessoaVendedor, $usuarioVendedor);
if ($usuarioAlunoId <= 0) {
    sairComMensagem('Nao foi possivel localizar ou criar o cadastro de aluno deste vendedor.');
}

$stmtAluno = $pdo->prepare("SELECT id, nome, cpf, nivel, ativo FROM usuarios WHERE id = :id LIMIT 1");
$stmtAluno->execute([':id' => $usuarioAlunoId]);
$aluno = $stmtAluno->fetch(PDO::FETCH_ASSOC) : [];
if (($aluno['nivel'] ?? '') !== 'Aluno' || ($aluno['ativo'] ?? '') !== 'Sim') {
    sairComMensagem('Conta de aluno invalida ou inativa.');
}

$_SESSION['switch_back_id'] = $usuarioSessao;
$_SESSION['switch_back_nivel'] = $nivelSessao;
$_SESSION['switch_back_nome'] = (string) ($_SESSION['nome'] ?? '');
$_SESSION['switch_back_cpf'] = (string) ($_SESSION['cpf'] ?? '');
$_SESSION['switch_vendedor_usuario_id'] = $usuarioVendedorId;

$_SESSION['id'] = (int) $aluno['id'];
$_SESSION['nivel'] = 'Aluno';
$_SESSION['nome'] = (string) ($aluno['nome'] ?? '');
$_SESSION['cpf'] = (string) ($aluno['cpf'] ?? '');

$idDestino = (int) $_SESSION['id'];
$nivelDestino = (string) $_SESSION['nivel'];
echo "<script>
try {
    localStorage.setItem('active_user_id', '{$idDestino}');
    localStorage.setItem('active_user_level', '{$nivelDestino}');
    localStorage.setItem('active_user_at', String(Date.now()));
} catch (e) {}
window.location.href = '../painel-aluno/index.php?pagina=home';
</script>";
exit();