<?php

require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/../config/csrf.php';

csrf_start();
csrf_token();

$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$nivelSessao = $_SESSION['nivel'] ?? '';
$niveisAdminPermitidos = ['Administrador', 'Professor', 'Secretario', 'Tesoureiro', 'Tutor', 'Parceiro', 'Assessor', 'Vendedor'];
$isAdminArea = strpos($scriptName, '/sistema/painel-admin/') !== false;
$isAlunoArea = strpos($scriptName, '/sistema/painel-aluno/') !== false;
$isRelArea = strpos($scriptName, '/sistema/rel/') !== false;
$isPanel = $isAdminArea || $isAlunoArea;
$isAjax = strpos($scriptName, '/ajax/') !== false;
$isGerarBoleto = strpos($scriptName, '/sistema/painel-aluno/paginas/gerar_boleto.php') !== false;
$isAtualizarVencimentoAluno = strpos($scriptName, '/sistema/painel-aluno/paginas/boletos/atualizar_vencimento.php') !== false;

$boletoAutorizado = false;
if ($isGerarBoleto && $method === 'POST') {
	$secret = env('BOLETO_AUTH_SECRET', '');
	$authToken = $_POST['auth_token'] ?? '';
	$authTs = (int) ($_POST['auth_ts'] ?? 0);
	$idParcela = (string) ($_POST['id_parcela'] ?? '');
	$idMatricula = (string) ($_POST['id_matricula'] ?? '');
	if ($secret !== '' && $authToken !== '' && $authTs > 0 && abs(time() - $authTs) <= 900) {
		$payload = $authTs . '|' . $idParcela . '|' . $idMatricula;
		$expected = hash_hmac('sha256', $payload, $secret);
		if (hash_equals($expected, $authToken)) {
			$boletoAutorizado = true;
		}
	}
}

$precisaVerificarNivelAdmin = $isAdminArea && !in_array($nivelSessao, $niveisAdminPermitidos, true);
$podeAtualizarVencimentoAluno = $isAtualizarVencimentoAluno && in_array($nivelSessao, ['Administrador', 'Secretario', 'Tesoureiro'], true);
$precisaVerificarNivelAluno = $isAlunoArea && $nivelSessao !== 'Aluno' && !$boletoAutorizado && !$podeAtualizarVencimentoAluno;
$precisaVerificarRel = $isRelArea && empty($nivelSessao);

if (($isPanel || $isAjax) && !$isGerarBoleto && $method !== 'GET' && $method !== 'HEAD' && $method !== 'OPTIONS') {
	csrf_require(false);
}

date_default_timezone_set('America/Porto_Velho');

$usuario = env('DB_USER', 'root');
$senha = env('DB_PASS', '');
$banco = env('DB_NAME', 'provao');
$servidor = env('DB_HOST', 'localhost');







try {
	$dsn = "mysql:host=$servidor;dbname=$banco;charset=utf8mb4";
	$options = [
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
	];
	$pdo = new PDO($dsn, "$usuario", "$senha", $options);
} catch (Exception $e) {
	echo 'Erro ao conectar ao banco de dados!<br><br>' . $e;
}

if ($precisaVerificarNivelAdmin || $precisaVerificarNivelAluno || $precisaVerificarRel) {
	if (!empty($_SESSION['id'])) {
		try {
			$stmtNivel = $pdo->prepare("SELECT nivel FROM usuarios WHERE id = :id LIMIT 1");
			$stmtNivel->execute([':id' => $_SESSION['id']]);
			$nivelBanco = (string) ($stmtNivel->fetchColumn() ?: '');
			if ($nivelBanco !== '') {
				$_SESSION['nivel'] = $nivelBanco;
				$nivelSessao = $nivelBanco;
				$precisaVerificarNivelAdmin = $isAdminArea && !in_array($nivelSessao, $niveisAdminPermitidos, true);
				$precisaVerificarNivelAluno = $isAlunoArea && $nivelSessao !== 'Aluno';
				$precisaVerificarRel = $isRelArea && empty($nivelSessao);
			}
		} catch (Exception $e) {
			// Mantem a validacao original se falhar ao consultar o banco
		}
	}

	if ($precisaVerificarNivelAdmin || $precisaVerificarNivelAluno || $precisaVerificarRel) {
		// Em navegacao direta no painel, redireciona para a tela de login.
		if ($isAdminArea && !$isAjax && ($method === 'GET' || $method === 'HEAD')) {
			header('Location: ../index.php');
			exit();
		}
		http_response_code(401);
		echo 'Nao autorizado.';
		exit();
	}
}

$host = $_SERVER['HTTP_HOST'];
$script_dir = dirname($_SERVER['SCRIPT_NAME']);
$script_dir = rtrim($script_dir, '/');
$url_path = $script_dir;
$strip_segments = [
	'/sistema',
	'/efi',
	'/ajax',
	'/scripts',
	'/pagamentos',
	'/pagamentos_novo',
	'/pgtos',
	'/api',
	'/webhooks',
];
$pos = null;
foreach ($strip_segments as $segment) {
	$found = strpos($url_path, $segment);
	if ($found !== false && ($pos === null || $found < $pos)) {
		$pos = $found;
	}
}
$url_path = $pos !== null ? substr($url_path, 0, $pos) : $url_path;
$url_path = rtrim($url_path, '/');
$forwardedProto = strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? ''));
$forwardedSsl = strtolower((string) ($_SERVER['HTTP_X_FORWARDED_SSL'] ?? ''));
$cfVisitor = (string) ($_SERVER['HTTP_CF_VISITOR'] ?? '');
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
	|| ($_SERVER['SERVER_PORT'] ?? 0) == 443
	|| $forwardedProto === 'https'
	|| $forwardedSsl === 'on'
	|| stripos($cfVisitor, '"scheme":"https"') !== false;
$scheme = $isHttps ? 'https' : 'http';
$url_sistema = "{$scheme}://{$host}{$url_path}";
$url_sistema = rtrim($url_sistema, '/') . '/';

//VARIÃVEIS DO SISTEMA

$nome_sistema = 'Sested-eja';

$email_sistema = 'sestedcursosvirtual@gmail.com';

$tel_sistema = '(69) 99969-4538';


//INSERIR DADOS INICIAIS NA TABELA CONFIG

$query = $pdo->query("SELECT * FROM config");

$res = $query->fetchAll(PDO::FETCH_ASSOC);

if (@count($res) == 0) {



	$stmtConfig = $pdo->prepare("INSERT INTO config SET nome_sistema = :nome_sistema, tel_sistema = :tel_sistema, email_sistema = :email_sistema, logo = 'logo.png', icone = 'favicon.ico', logo_rel = 'logo.jpg', itens_pag = '18', cartoes_fidelidade = '5', valor_max_cartao = '100', total_emails_por_envio = '480', intervalo_envio_email = '70', dias_email_matricula = '3', dias_excluir_matricula = '30', script_dia = curDate(), questionario = 'Sim', media = '60' ");
	$stmtConfig->execute([
		':nome_sistema' => $nome_sistema,
		':tel_sistema' => $tel_sistema,
		':email_sistema' => $email_sistema,
	]);

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
	
	$acrescimo_cartao_credito = $res[0]['acrescimo_cartao_credito'];

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
