<?php
chdir(__DIR__ . '/../sested');
require __DIR__ . '/../sested/sistema/conexao.php';

$mat = $pdo->query("SELECT id, aluno, id_curso, professor, forma_pgto, status FROM matriculas WHERE id = 973")->fetch(PDO::FETCH_ASSOC);
$alunoUserId = (int)($mat['aluno'] ?? 0);
$alunoUser = $pdo->query("SELECT id, nome, usuario, nivel, id_pessoa FROM usuarios WHERE id = $alunoUserId")->fetch(PDO::FETCH_ASSOC);
$alunoPessoaId = (int)($alunoUser['id_pessoa'] ?? 0);
$alunoCad = $pdo->query("SELECT id, nome, usuario, responsavel_id FROM alunos WHERE id = $alunoPessoaId")->fetch(PDO::FETCH_ASSOC);
$profUserId = (int)($mat['professor'] ?? 0);
$profUser = $pdo->query("SELECT id, nome, nivel, wallet_id FROM usuarios WHERE id = $profUserId")->fetch(PDO::FETCH_ASSOC);

$laura = $pdo->query("SELECT id, nome, nivel, wallet_id, id_pessoa FROM usuarios WHERE nome LIKE '%LAURA MARIA JONJOB DE SOUZA%' LIMIT 1")->fetch(PDO::FETCH_ASSOC);

echo "MATRICULA\n".json_encode($mat,JSON_UNESCAPED_UNICODE)."\n\n";
echo "USUARIO_ALUNO\n".json_encode($alunoUser,JSON_UNESCAPED_UNICODE)."\n\n";
echo "CADASTRO_ALUNO\n".json_encode($alunoCad,JSON_UNESCAPED_UNICODE)."\n\n";
echo "USUARIO_PROFESSOR_DA_MATRICULA\n".json_encode($profUser,JSON_UNESCAPED_UNICODE)."\n\n";
echo "USUARIO_LAURA\n".json_encode($laura,JSON_UNESCAPED_UNICODE)."\n";
?>