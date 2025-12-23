<?php 
require_once('../../sistema/conexao.php');
@session_start();
$id_aluno = $_SESSION['id'];


$id_curso = @$_POST['id_curso_cartao'];

//nome do curso
$query2 = $pdo->query("SELECT * FROM cursos where id = '$id_curso'");
$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
$nome_curso = $res2[0]['nome'];
$valor_curso = $res2[0]['valor'];
$matriculas = $res2[0]['matriculas'];
$quantid_mat = $matriculas + 1;

//verificar se o aluno possui 5 cartoes
$query2 = $pdo->query("SELECT * FROM usuarios where id = '$id_aluno'");
$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
if(@count($res2) > 0){
	$id_pessoa = $res2[0]['id_pessoa'];
	$nome_aluno = $res2[0]['nome'];
		$query3 = $pdo->query("SELECT * FROM alunos where id = '$id_pessoa'");
		$res3 = $query3->fetchAll(PDO::FETCH_ASSOC);
		if(@count($res3) > 0){
			$quant_cartoes = $res3[0]['cartao'];
			if(@$quant_cartoes < $cartoes_fidelidade and $valor_curso <= $valor_max_cartao){
				exit();
			}
		}
}

//libera a matricula
$pdo->query("UPDATE matriculas SET status = 'Matriculado', subtotal = '0', obs = 'Cartão Fidelidade' where aluno = '$id_aluno' and id_curso = '$id_curso'");

//zerar o cartão
$pdo->query("UPDATE alunos SET cartao = '0' where id = '$id_pessoa'");


//incrementar um aluno na matricula
$pdo->query("UPDATE cursos SET matriculas = '$quantid_mat' where id = '$id_curso'");


//mande email alertando uso do cartão
$destinatario = $email_sistema;
$assunto = 'Cartão Fidelidade usado no ' .$nome_curso;

$mensagem = "O Aluno $nome_aluno, usou um cartão fidelidade no curso $nome_curso!!";

$remetente = $email_sistema;
$cabecalhos = 'MIME-Version: 1.0' . "\r\n";
$cabecalhos .= 'Content-type: text/html; charset=utf-8;' . "\r\n";
$cabecalhos .= "From: " .$remetente;
@mail($destinatario, $assunto, $mensagem, $cabecalhos);

echo 'Cartão Utilizado';

?>