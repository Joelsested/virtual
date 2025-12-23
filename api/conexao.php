<?php 

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With'); 
header('Content-Type: application/json; charset=utf-8');  


date_default_timezone_set('America/Sao_Paulo');


// $usuario = 'hugocu75_portalead';
// $senha = 'portalead';
// $banco = 'hugocu75_portalead';
// $host = 'sh-pro24.hostgator.com.br';

// $usuario ='sested59_sestedead';
// $senha ='EAD157sestedead@#$';
// $banco ='sested59_sestedead';
// $servidor ='sh-pro60.hostgator.com.br';

$usuario = 'sested59_provao';
$senha = 'provao157@#$';
$banco = 'sested59_sested_eja_novo';
$servidor = 'sh-pro60.hostgator.com.br';
try {
	$pdo = new PDO("mysql:dbname=$banco; host=$host", "$usuario", "$senha");
	
} catch (Exception $e) {
	echo 'Erro ao conectar com o banco!!' .$e;
}

$url_sistema = "http://$_SERVER[HTTP_HOST]/";
$url = explode("//", $url_sistema);
if($url[1] == 'localhost/'){
	$url_sistema = "http://$_SERVER[HTTP_HOST]/portalead/";
}

//INSERIR DADOS INICIAIS NA TABELA CONFIG
$query = $pdo->query("SELECT * FROM config");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
if(@count($res) == 0){
	
	$pdo->query("INSERT INTO config SET nome_sistema = '$nome_sistema', tel_sistema = '$tel_sistema', email_sistema = '$email_sistema', logo = 'logo.png', icone = 'favicon.ico', logo_rel = 'logo.jpg', itens_pag = '18', cartoes_fidelidade = '5', valor_max_cartao = '100', total_emails_por_envio = '480', intervalo_envio_email = '70', dias_email_matricula = '3', dias_excluir_matricula = '30', script_dia = curDate() ");
}else{
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

$tel_whatsapp = '55'.preg_replace('/[ ()-]+/' , '' , $tel_sistema);
}


?>