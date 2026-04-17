<?php

require_once("../../../conexao.php");
$hoje = date('Y-m-d');
$mes_atual = Date('m');
$ano_atual = Date('Y');
$data_pgto_comissao = $ano_atual.'-'.$mes_atual.'-'.$dia_pgto_comissao;

$forma_pgto = $_POST['forma_pgto'];
$subtotal = $_POST['valor'];
$subtotal = str_replace(',', '.', $subtotal);
$obs = $_POST['obs'];
$cartao = $_POST['cartao'];
$id_mat = $_POST['id_mat'];

$total_recebido = $subtotal;

if($forma_pgto == 'MP'){
$total_recebido = $subtotal - ($subtotal * ($taxa_mp / 100));
}

if($forma_pgto == 'Boleto'){
$total_recebido = $subtotal - $taxa_boleto;
}

if($forma_pgto == 'Paypal'){		
	$total_recebido = $subtotal - ($subtotal * ($taxa_paypal / 100)); ;
}


$query = $pdo->query("SELECT * FROM matriculas where id = '$id_mat'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
if(@count($res) > 0){	
	$pacote = $res[0]['pacote'];
	$aluno = $res[0]['aluno'];
	$id_curso = $res[0]['id_curso'];
	$status_mat = $res[0]['status'];
	$professor = $res[0]['professor'];
	
	if($pacote == 'Sim'){
		$tab = 'pacotes';
	}else{
		$tab = 'cursos';
	}

}

$query = $pdo->query("SELECT * FROM usuarios where id = '$aluno'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
if(@count($res) > 0){
	$nome_aluno = $res[0]['nome'];
	$email_aluno = $res[0]['usuario'];
	$id_pessoa_aluno = $res[0]['id_pessoa'];
}

$query = $pdo->query("SELECT * FROM alunos where id = '$id_pessoa_aluno'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
if(@count($res) > 0){
	$cartoes = $res[0]['cartao'];
	$usuario_comissao = $res[0]['usuario'];
}





$query = $pdo->query("SELECT * FROM usuarios where id = '$usuario_comissao'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
if(@count($res) > 0){
	$nivel_do_usu = $res[0]['nivel'];
}




$query = $pdo->query("SELECT * FROM usuarios where id = '$professor'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
if(@count($res) > 0){
	$email_professor = $res[0]['usuario'];
}





//ATUALIZANDO A MATRÃCULA
$query = $pdo->prepare("UPDATE matriculas SET status = 'Matriculado', forma_pgto = '$forma_pgto', total_recebido = :total_recebido, data = curDate(), obs = :obs  where id = '$id_mat'");

$query->bindValue(":total_recebido", "$total_recebido");
$query->bindValue(":obs", "$obs");
$query->execute();


if($cartao == 'Sim'){
	//ADICIONAR MAIS UM CARTÃƒO PARA O ALUNO
$cartoes += 1;
$pdo->query("UPDATE alunos SET cartao = '$cartoes' where id = '$id_pessoa_aluno'");
}



//LIBERAR OS CURSOS SE FOR UM PACOTE
if($pacote == 'Sim'){
	$query = $pdo->query("SELECT * FROM cursos_pacotes where id_pacote = '$id_curso' order by id desc");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$total_reg = @count($res);

	if($total_reg > 0){
		for($i=0; $i < $total_reg; $i++){
		foreach ($res[$i] as $key => $value){}
		$id_cursos_pacotes = $res[$i]['id'];
		$id_do_curso = $res[$i]['id_curso'];

		$query2 = $pdo->query("SELECT * FROM cursos where id = '$id_do_curso'");
		$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
		$matriculas = $res2[0]['matriculas'];
		$id_professor = $res2[0]['professor'];
		$quant_mat = $matriculas + 1; 

				
		$query3 = $pdo->query("SELECT * FROM matriculas where id_curso = '$id_do_curso' and aluno = '$aluno'");
		$res3 = $query3->fetchAll(PDO::FETCH_ASSOC);
		

		if(@count($res3) > 0){	
			$id_mat = @$res3[0]['id'];
			//excluir a matrÃ­cula do curso se ela jÃ¡ existir
			$pdo->query("DELETE FROM matriculas where id = '$id_mat'");
		}
			//inserir a matrÃ­cula do curso caso ela nÃ£o exista
			$pdo->query("INSERT INTO matriculas SET id_curso = '$id_do_curso', aluno = '$aluno', professor = '$id_professor', aulas_concluidas = '1', data = curDate(), status = 'Matriculado', pacote = 'NÃ£o', id_pacote = '$id_curso', obs = 'Pacote' ");


			//atualizar matriculas do curso
			$pdo->query("UPDATE cursos SET matriculas = '$quant_mat' where id = '$id_do_curso'");
				

		}
	}

}

//ADICIONAR MAIS UMA VENDA AO CURSO OU PACOTE
$query2 = $pdo->query("SELECT * FROM $tab where id = '$id_curso'");
$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
$matriculas = $res2[0]['matriculas'];
$valor_comissao = 0;
$quantid_mat = $matriculas + 1;
$nome_curso = $res2[0]['nome'];
$pdo->query("UPDATE $tab SET matriculas = '$quantid_mat' where id = '$id_curso'");


if (!empty($nivel_do_usu)) {
	$stmtNivel = $pdo->prepare("SELECT porcentagem FROM comissoes WHERE nivel = :nivel LIMIT 1");
	$stmtNivel->bindValue(':nivel', $nivel_do_usu);
	$stmtNivel->execute();
	$registroNivel = $stmtNivel->fetch(PDO::FETCH_ASSOC);
	if (!empty($registroNivel)) {
		$valor_comissao = (float)$registroNivel['porcentagem'];
	}
}

if ($valor_comissao <= 0) {
	if ($nivel_do_usu == 'Professor') {
		$valor_comissao = $comissao_professor;
	} else if ($nivel_do_usu == 'Tutor') {
		$valor_comissao = $comissao_tutor;
	} else if ($nivel_do_usu == 'Parceiro') {
		$valor_comissao = $comissao_parceiro;
	} else if ($nivel_do_usu == 'Assessor') {
		$valor_comissao = $comissao_assessor;
	} else if ($nivel_do_usu == 'Vendedor') {
		$valor_comissao = $comissao_vendedor;
	} else if ($nivel_do_usu == 'Tesoureiro') {
		$valor_comissao = $comissao_tesoureiro;
	} else if ($nivel_do_usu == 'Secretario') {
		$valor_comissao = $comissao_secretario;
	}
}

if ((int)$dia_pgto_comissao <= 0) {
	$dia_pgto_comissao = 20;
}
if ((int)$dia_pgto_comissao > 28) {
	$dia_pgto_comissao = 28;
}
$data_pgto_comissao = $ano_atual . '-' . $mes_atual . '-' . str_pad($dia_pgto_comissao, 2, '0', STR_PAD_LEFT);

$valor_comissao_pagar = round(($valor_comissao * $subtotal) / 100, 2);
if(strtotime($hoje) < strtotime($data_pgto_comissao)){
	$data_venc = $data_pgto_comissao;
}else{
	$data_venc = date('Y-m-d', strtotime("+1 month",strtotime($data_pgto_comissao)));
}

if ($valor_comissao_pagar > 0 && !empty($usuario_comissao)) {
	$queryComissao = $pdo->prepare("INSERT INTO pagar SET descricao = 'Comissão', valor = :valor, data = curDate(), vencimento = :vencimento, pago = 'Não', arquivo = 'sem-foto.png', professor = :professor, curso = :curso");
	$queryComissao->bindValue(':valor', $valor_comissao_pagar);
	$queryComissao->bindValue(':vencimento', $data_venc);
	$queryComissao->bindValue(':professor', $usuario_comissao);
	$queryComissao->bindValue(':curso', $nome_curso);
	$queryComissao->execute();
}

$queryFixas = $pdo->query("SELECT nivel, porcentagem FROM comissoes WHERE recebeSempre = 1");
$comissoesFixas = $queryFixas->fetchAll(PDO::FETCH_ASSOC);
if (@count($comissoesFixas) > 0) {
	$queryUsuariosFixos = $pdo->prepare("SELECT id FROM usuarios WHERE nivel = :nivel");
	$queryInsertFixa = $pdo->prepare("INSERT INTO pagar SET descricao = 'Comissão', valor = :valor, data = curDate(), vencimento = :vencimento, pago = 'Não', arquivo = 'sem-foto.png', professor = :professor, curso = :curso");

	for ($cf = 0; $cf < @count($comissoesFixas); $cf++) {
		$nivelFixo = $comissoesFixas[$cf]['nivel'];
		$porcentagemFixa = (float)$comissoesFixas[$cf]['porcentagem'];
		if ($porcentagemFixa <= 0) {
			continue;
		}

		$valorComissaoFixa = round(($porcentagemFixa * $subtotal) / 100, 2);
		if ($valorComissaoFixa <= 0) {
			continue;
		}

		$queryUsuariosFixos->bindValue(':nivel', $nivelFixo);
		$queryUsuariosFixos->execute();
		$usuariosFixos = $queryUsuariosFixos->fetchAll(PDO::FETCH_ASSOC);

		for ($uf = 0; $uf < @count($usuariosFixos); $uf++) {
			$usuarioFixo = $usuariosFixos[$uf]['id'];
			if (empty($usuarioFixo)) {
				continue;
			}

			$queryInsertFixa->bindValue(':valor', $valorComissaoFixa);
			$queryInsertFixa->bindValue(':vencimento', $data_venc);
			$queryInsertFixa->bindValue(':professor', $usuarioFixo);
			$queryInsertFixa->bindValue(':curso', $nome_curso);
			$queryInsertFixa->execute();
		}
	}
}

echo 'Matriculado com Sucesso';

//ENVIAR EMAIL PARA O ADM, PROFESSOR E ALUNO
require_once('../../../../pagamentos/email-aprovar-matricula.php');







?>
