<?php
date_default_timezone_set('America/Porto_Velho');


//servidor hospedado
$usuario = 'sested59_pro_efi';
$senha = 'sested59_pro_efi';
$banco = 'sested59_pro_efi';
$servidor = 'sh-pro60.hostgator.com.br';

///servidor local
// $usuario = 'root';
// $senha = '';
// $banco = 'sestedeja_novo';
// $servidor = 'localhost';



try {
	$pdo = new PDO("mysql:dbname=$banco;host=$servidor", "$usuario", "$senha");
} catch (Exception $e) {
	echo 'Erro ao conectar ao banco de dados!<br><br>' . $e;
}

$url_sistema = "https://$_SERVER[HTTP_HOST]/";
$url = explode("//", $url_sistema);
if ($url[1] == 'localhost/') {
	$url_sistema = "https://$_SERVER[HTTP_HOST]/sestedead/";
}


//VARIÁVEIS DO SISTEMA
$nome_sistema = 'Sested';
$email_sistema = 'sestedcursosvirtual@gmail.com';
$tel_sistema = '(69) 99969-4538';


//INSERIR DADOS INICIAIS NA TABELA CONFIG
$query = $pdo->query("SELECT * FROM config");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
if (@count($res) == 0) {

	$pdo->query("INSERT INTO config SET nome_sistema = '$nome_sistema', tel_sistema = '$tel_sistema', email_sistema = '$email_sistema', logo = 'logo.png', icone = 'favicon.ico', logo_rel = 'logo.jpg', itens_pag = '18', cartoes_fidelidade = '5', valor_max_cartao = '100', total_emails_por_envio = '480', intervalo_envio_email = '70', dias_email_matricula = '3', dias_excluir_matricula = '30', script_dia = curDate(), questionario = 'Sim', media = '60' ");
} else {
	$nome_sistema = $res[0]['nome_sistema'];
	$email_sistema = $res[0]['email_sistema'];
	$tel_sistema = $res[0]['tel_sistema'];
	$cnpj_sistema = $res[0]['cnpj_sistema'];
	$tipo_chave_pix = $res[0]['tipo_chave_pix'];
	$chave_pix = $res[0]['chave_pix'];
	$facebook_sistema = $res[0]['facebook'];
	$instagram_sistema = $res[0]['instagram'];
	$youtube_sistema = $res[0]['youtube'];
	$itens_pag = $res[0]['itens_pag'];
	$video_sobre = $res[0]['video_sobre'];
	$aulas_lib = $res[0]['aulas_liberadas'];
	$itens_rel = $res[0]['itens_relacionados'];
	$desconto_pix = $res[0]['desconto_pix'];
	$email_adm_mat = $res[0]['email_adm_mat'];
	$cartoes_fidelidade = $res[0]['cartoes_fidelidade'];
	$taxa_mp = $res[0]['taxa_mp'];
	$taxa_paypal = $res[0]['taxa_paypal'];
	$taxa_boleto = $res[0]['taxa_boleto'];
	$valor_max_cartao = $res[0]['valor_max_cartao'];
	$total_emails_por_envio = $res[0]['total_emails_por_envio'];
	$intervalo_envio_email = $res[0]['intervalo_envio_email'];
	$dias_email_matricula = $res[0]['dias_email_matricula'];
	$dias_excluir_matricula = $res[0]['dias_excluir_matricula'];
	$script_dia = $res[0]['script_dia'];
	$professor_cad = $res[0]['professor_cad'];
	$comissao_professor = $res[0]['comissao_professor'];
	$dia_pgto_comissao = $res[0]['dia_pgto_comissao'];
	$questionario_config = $res[0]['questionario'];
	$media_config = $res[0]['media'];
	$verso = $res[0]['verso'];
	@$api_cartao = $res[0]['api_cartao'];

	$comissao_tutor = $res[0]['comissao_tutor'];
	$comissao_parceiro = $res[0]['comissao_parceiro'];
	$comissao_tesoureiro = $res[0]['comissao_tesoureiro'];
	$comissao_secretario = $res[0]['comissao_secretario'];
	$comissao_assessor = $res[0]['comissao_assessor'];
	$comissao_vendedor = $res[0]['comissao_vendedor'];

	$tel_whatsapp = '55' . preg_replace('/[ ()-]+/', '', $tel_sistema);
}


if ($script_dia != date('Y-m-d')) {
	require_once('verificar-scripts.php');
}

?>