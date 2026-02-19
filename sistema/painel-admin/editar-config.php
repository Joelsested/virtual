<?php 
require_once("../conexao.php");
require_once("verificar.php");
require_once(__DIR__ . "/../../config/upload.php");

@session_start();
$nivel_config = $_SESSION['nivel'] ?? '';
if (!in_array($nivel_config, ['Administrador', 'Tesoureiro', 'Secretario'], true)) {
	echo 'Sem permissao.';
	exit();
}

$nome_sistema = trim($_POST['nome_sistema'] ?? '');
$email_sistema = trim($_POST['email_sistema'] ?? '');
$tel_sistema = trim($_POST['tel_sistema'] ?? '');
$cnpj_sistema = trim($_POST['cnpj_sistema'] ?? '');
$tipo_chave_pix = trim($_POST['tipo_chave_pix_sistema'] ?? '');
$chave_pix = trim($_POST['chave_pix'] ?? '');
$facebook_sistema = trim($_POST['facebook_sistema'] ?? '');
$instagram_sistema = trim($_POST['instagram_sistema'] ?? '');
$youtube_sistema = trim($_POST['youtube_sistema'] ?? '');
$itens_pag = trim($_POST['itens_pag'] ?? '');
$video_sobre = trim($_POST['video_sobre'] ?? '');
$aulas_lib = trim($_POST['aulas_lib'] ?? '');
$itens_rel = trim($_POST['itens_rel'] ?? '');
$desconto_pix = trim($_POST['desconto_pix'] ?? '');
$acrescimo_cartao_credito = trim($_POST['acrescimo_cartao_credito'] ?? '');
$email_adm_mat = trim($_POST['email_adm_mat'] ?? '');
$cartoes_fidelidade = trim($_POST['cartoes_fidelidade'] ?? '');
$taxa_mp = trim($_POST['taxa_mp'] ?? '');
$taxa_paypal = trim($_POST['taxa_paypal'] ?? '');
$taxa_boleto = trim($_POST['taxa_boleto'] ?? '');
$valor_max_cartao = trim($_POST['valor_max_cartao'] ?? '');
$total_emails_por_envio = trim($_POST['total_emails_por_envio'] ?? '');
$intervalo_envio_email = trim($_POST['intervalo_envio_email'] ?? '');
$dias_email_matricula = trim($_POST['dias_email_matricula'] ?? '');
$dias_excluir_matricula = trim($_POST['dias_excluir_matricula'] ?? '');
$professor_cad = trim($_POST['professor_cad'] ?? '');
$comissao_professor = trim($_POST['comissao_professor'] ?? '');
$dia_pgto_comissao = trim($_POST['dia_pgto_comissao'] ?? '');
$questionario = trim($_POST['questionario'] ?? '');
$media = trim($_POST['media'] ?? '');
$verso = trim($_POST['verso'] ?? '');
$api_cartao = trim($_POST['api_cartao'] ?? '');





$comissao_tesoureiro = trim($_POST['comissao_tesoureiro'] ?? '');
$comissao_secretario = trim($_POST['comissao_secretario'] ?? '');
$comissao_tutor = trim($_POST['comissao_tutor'] ?? '');
$comissao_parceiro = trim($_POST['comissao_parceiro'] ?? '');
$comissao_assessor = trim($_POST['comissao_assessor'] ?? '');
$comissao_vendedor = trim($_POST['comissao_vendedor'] ?? '');

function normalize_decimal($value) {
	$value = str_replace(',', '.', trim((string) $value));
	if ($value === '') {
		return '';
	}
	return is_numeric($value) ?? $value : '';
}

function validate_percent($value, $label) {
	if ($value === '') {
		return;
	}
	if (!is_numeric($value) || $value < 0 || $value > 100) {
		echo $label . ' deve estar entre 0 e 100.';
		exit();
	}
}

$taxa_mp = normalize_decimal($taxa_mp);
$taxa_paypal = normalize_decimal($taxa_paypal);
$taxa_boleto = normalize_decimal($taxa_boleto);
$valor_max_cartao = normalize_decimal($valor_max_cartao);
$desconto_pix = normalize_decimal($desconto_pix);
$acrescimo_cartao_credito = normalize_decimal($acrescimo_cartao_credito);
$comissao_professor = normalize_decimal($comissao_professor);
$comissao_tesoureiro = normalize_decimal($comissao_tesoureiro);
$comissao_secretario = normalize_decimal($comissao_secretario);
$comissao_tutor = normalize_decimal($comissao_tutor);
$comissao_parceiro = normalize_decimal($comissao_parceiro);
$comissao_assessor = normalize_decimal($comissao_assessor);
$comissao_vendedor = normalize_decimal($comissao_vendedor);
$media = normalize_decimal($media);

validate_percent($desconto_pix, 'Desconto Pix');
validate_percent($acrescimo_cartao_credito, 'Acrescimo Cartao');
validate_percent($comissao_professor, 'Comissao Professor');
validate_percent($comissao_tesoureiro, 'Comissao Tesoureiro');
validate_percent($comissao_secretario, 'Comissao Secretario');
validate_percent($comissao_tutor, 'Comissao Tutor');
validate_percent($comissao_parceiro, 'Comissao Parceiro');
validate_percent($comissao_assessor, 'Comissao Assessor');
validate_percent($comissao_vendedor, 'Comissao Vendedor');


//SCRIPT PARA SUBIR FOTO NO SERVIDOR
$baseDir = __DIR__ . '/../img';
$uploadLogo = upload_handle_fixed($_FILES['logo'] ?? [], $baseDir . '/logo.png', ['png'], ['image/png'], 2 * 1024 * 1024, true);
if (!$uploadLogo['ok']) {
	echo $uploadLogo['error'];
	exit();
}

$uploadFavicon = upload_handle_fixed($_FILES['favicon'] ?? [], $baseDir . '/favicon.ico', ['ico'], ['image/x-icon', 'image/vnd.microsoft.icon'], 512 * 1024, true);
if (!$uploadFavicon['ok']) {
	echo $uploadFavicon['error'];
	exit();
}

$uploadRel = upload_handle_fixed($_FILES['imgRel'] ?? [], $baseDir . '/logo_rel.jpg', ['jpg', 'jpeg'], ['image/jpeg'], 2 * 1024 * 1024, true);
if (!$uploadRel['ok']) {
	echo $uploadRel['error'];
	exit();
}

$uploadQr = upload_handle_fixed($_FILES['imgQRCode'] ?? [], $baseDir . '/qrcode.jpg', ['jpg', 'jpeg'], ['image/jpeg'], 2 * 1024 * 1024, true);
if (!$uploadQr['ok']) {
	echo $uploadQr['error'];
	exit();
}

// Atualizar variaveis EFI no .env (se informado no formulario) ?? $envUpdates = [];
$efi_sandbox = trim($_POST['efi_sandbox'] ?? '');
$efi_client_id_prod = trim($_POST['efi_client_id_prod'] ?? '');
$efi_client_secret_prod = trim($_POST['efi_client_secret_prod'] ?? '');
$efi_client_id_homolog = trim($_POST['efi_client_id_homolog'] ?? '');
$efi_client_secret_homolog = trim($_POST['efi_client_secret_homolog'] ?? '');
$efi_webhook_url_prod = trim($_POST['efi_webhook_url_prod'] ?? '');
$efi_webhook_path_prod = trim($_POST['efi_webhook_path_prod'] ?? '');
$efi_webhook_url_homolog = trim($_POST['efi_webhook_url_homolog'] ?? '');
$efi_webhook_path_homolog = trim($_POST['efi_webhook_path_homolog'] ?? '');
$efi_webhook_base_url = trim($_POST['efi_webhook_base_url'] ?? '');
$efi_cert_target = trim($_POST['efi_cert_target'] ?? 'prod');
$efi_cert_password = trim($_POST['efi_cert_password'] ?? '');
$efi_client_id_selected = trim($_POST['efi_client_id_selected'] ?? '');
$efi_client_secret_selected = trim($_POST['efi_client_secret_selected'] ?? '');
$efi_webhook_url_selected = trim($_POST['efi_webhook_url_selected'] ?? '');
$efi_webhook_path_selected = trim($_POST['efi_webhook_path_selected'] ?? '');
$cert_password_sandbox = trim($_POST['cert_password_sandbox'] ?? '');
$cert_password_producao = trim($_POST['cert_password_producao'] ?? '');
$certPathProd = '';
$certPathHomolog = '';

// Fallback seguro: se o JS nao sincronizar os campos ocultos, usa o ambiente selecionado.
if ($efi_sandbox === 'true') {
	if ($efi_client_id_selected !== '') {
		$efi_client_id_homolog = $efi_client_id_selected;
	}
	if ($efi_client_secret_selected !== '') {
		$efi_client_secret_homolog = $efi_client_secret_selected;
	}
	if ($efi_webhook_url_selected !== '') {
		$efi_webhook_url_homolog = $efi_webhook_url_selected;
	}
	if ($efi_webhook_path_selected !== '') {
		$efi_webhook_path_homolog = $efi_webhook_path_selected;
	}
} else {
	if ($efi_client_id_selected !== '') {
		$efi_client_id_prod = $efi_client_id_selected;
	}
	if ($efi_client_secret_selected !== '') {
		$efi_client_secret_prod = $efi_client_secret_selected;
	}
	if ($efi_webhook_url_selected !== '') {
		$efi_webhook_url_prod = $efi_webhook_url_selected;
	}
	if ($efi_webhook_path_selected !== '') {
		$efi_webhook_path_prod = $efi_webhook_path_selected;
	}
}

$envUpdates['EFI_SANDBOX'] = ($efi_sandbox === 'true') ? 'true' : 'false';
if ($efi_client_id_prod !== '') {
	$envUpdates['EFI_CLIENT_ID_PROD'] = $efi_client_id_prod;
}
if ($efi_client_secret_prod !== '') {
	$envUpdates['EFI_CLIENT_SECRET_PROD'] = $efi_client_secret_prod;
}
if ($efi_client_id_homolog !== '') {
	$envUpdates['EFI_CLIENT_ID_HOMOLOG'] = $efi_client_id_homolog;
}
if ($efi_client_secret_homolog !== '') {
	$envUpdates['EFI_CLIENT_SECRET_HOMOLOG'] = $efi_client_secret_homolog;
}
if ($efi_webhook_base_url !== '') {
	$envUpdates['EFI_WEBHOOK_BASE_URL'] = $efi_webhook_base_url;
}

$base_ecossistema = dirname(__DIR__, 3);
$slug_env = '';
$mapa_slug_env = [1 => 'virtual', 2 => 'provao', 3 => 'sestedcursos'];
if (!empty($_POST['sistema_id_alvo'])) {
	$sistema_id_alvo = (int) $_POST['sistema_id_alvo'];
	$slug_env = $mapa_slug_env[$sistema_id_alvo] ?? '';
	if (empty($slug_env)) {
		try {
			$stmtSlug = $pdo->prepare("SELECT slug FROM sistemas WHERE id = :id LIMIT 1");
			$stmtSlug->execute([':id' => $sistema_id_alvo]);
			$slugDb = $stmtSlug->fetchColumn();
			if (!empty($slugDb)) {
				$slug_env = (string) $slugDb;
			}
		} catch (Exception $e) {
			// Sem impacto, segue fallback.
		}
	}
}
if ($slug_env === '' && !empty($_POST['sistema_slug_alvo'])) {
	$slug_env = preg_replace('/[^a-z0-9_-]/i', '', (string) $_POST['sistema_slug_alvo']);
}
if ($slug_env === '' && isset($_SESSION['sistema_id'])) {
	$slug_env = $mapa_slug_env[(int) $_SESSION['sistema_id']] ?? '';
}
if ($slug_env === '' && isset($sistema_slug_atual) && $sistema_slug_atual !== '') {
	$slug_env = $sistema_slug_atual;
}
if ($slug_env === '') {
	$slug_env = basename(dirname(__DIR__, 3));
}
$slug_env = strtolower(trim((string)$slug_env));
$slug_alias = ['capacitacoes' => 'sestedcursos', 'sestedcursos' => 'sestedcursos', 'provao' => 'provao', 'virtual' => 'virtual', 'sested-virtual' => 'virtual', 'sested_virtual' => 'virtual',
];
if (isset($slug_alias[$slug_env])) {
	$slug_env = $slug_alias[$slug_env];
}
$envBaseDir = $base_ecossistema . '/' . $slug_env;
if (!is_dir($envBaseDir)) {
	echo 'Sistema alvo invalido.';
	exit();
}
$certDirBase = $base_ecossistema . '/' . $slug_env . '/efi_obp/certs';

if (!function_exists('carregar_env_map')) {
	function carregar_env_map(string $envPath) : array {
		if (!is_file($envPath)) {
			return [];
		}
		$map = [];
		$lines = file($envPath, FILE_IGNORE_NEW_LINES);
		foreach ($lines as $line) {
			$line = trim((string) $line);
			if ($line === '' || str_starts_with($line, '#')) {
				continue;
			}
			$parts = explode('=', $line, 2);
			$key = trim($parts[0] ?? '');
			if ($key === '') {
				continue;
			}
			$map[$key] = $parts[1] ?? '';
		}
		return $map;
	}
}

if (!function_exists('normalizar_path')) {
	function normalizar_path(string $path) : string {
		return str_replace('\\', '/', $path);
	}
}

if (!function_exists('resolver_path_certificado')) {
	function resolver_path_certificado(string $path, string $certDirBase) : string {
		$path = trim($path);
		if ($path === '') {
			return '';
		}
		$pathNorm = normalizar_path($path);
		$real = realpath($pathNorm);
		if ($real === false) {
			$real = realpath($certDirBase . '/' . ltrim($pathNorm, '/'));
		}
		if ($real === false) {
			return '';
		}
		$realNorm = normalizar_path($real);
		$certBaseReal = realpath($certDirBase);
		if ($certBaseReal === false) {
			$certBaseReal = $certDirBase;
		}
		$certBaseNorm = rtrim(normalizar_path($certBaseReal), '/');
		if ($realNorm !== $certBaseNorm && strpos($realNorm, $certBaseNorm . '/') !== 0) {
			return '';
		}
		return $realNorm;
	}
}

if (!function_exists('limpar_certificado_antigo')) {
	function limpar_certificado_antigo(string $oldPath, string $newPath, string $certDirBase) : void {
		$oldPath = trim($oldPath);
		$newPath = trim($newPath);
		if ($oldPath === '' || $newPath === '') {
			return;
		}
		if (normalizar_path($oldPath) === normalizar_path($newPath)) {
			return;
		}
		$oldReal = resolver_path_certificado($oldPath, $certDirBase);
		if ($oldReal === '') {
			return;
		}
		$base = preg_replace('/\.(pem|p12)$/i', '', $oldReal);
		$pem = $base . '.pem';
		$p12 = $base . '.p12';
		if (is_file($pem)) {
			@unlink($pem);
		}
		if (is_file($p12)) {
			@unlink($p12);
		}
	}
}

if (!function_exists('processar_certificado_efi')) {
	function processar_certificado_efi(array $file, string $senha, string $target, array &$envUpdates, string &$certPathProd, string &$certPathHomolog, string $certDirBase) : void {
		if (empty($file['tmp_name'])) {
			return;
		}
		if (!function_exists('openssl_pkcs12_read')) {
			echo 'Extensao OpenSSL nao habilitada no PHP.';
			exit();
		}
		$p12Content = file_get_contents($file['tmp_name']);
		$certs = [];
		if (!openssl_pkcs12_read($p12Content, $certs, $senha)) {
			echo 'Falha ao ler o certificado .p12. Verifique a senha.';
			exit();
		}
		$certDir = $certDirBase !== '' ? $certDirBase : (dirname(__DIR__, 2) . '/efi_obp/certs');
		if (!is_dir($certDir)) {
			@mkdir($certDir, 0755, true);
		}
		$baseName = 'efi-cert-' . date('Y-m-d-H-i-s') . '-' . bin2hex(random_bytes(3));
		$p12Path = $certDir . '/' . $baseName . '.p12';
		$pemPath = $certDir . '/' . $baseName . '.pem';
		$originalName = isset($file['name']) ? basename((string)$file['name']) : '';
		if (!move_uploaded_file($file['tmp_name'], $p12Path)) {
			echo 'Falha ao salvar o certificado .p12.';
			exit();
		}
		$pemData = ($certs['cert'] ?? '') . "\n" . ($certs['pkey'] ?? '');
		if (!empty($certs['extracerts']) && is_array($certs['extracerts'])) {
			$pemData .= "\n" . implode("\n", $certs['extracerts']);
		}
		if (trim($pemData) === '') {
			echo 'Certificado .p12 invalido.';
			exit();
		}
		file_put_contents($pemPath, $pemData);
		$pemPathEnv = str_replace('\\', '/', $pemPath);
		if ($target === 'homolog') {
			$certPathHomolog = $pemPathEnv;
			$envUpdates['EFI_CERT_PATH_HOMOLOG'] = $pemPathEnv;
			if ($originalName !== '') {
				$envUpdates['EFI_CERT_NAME_HOMOLOG'] = $originalName;
			}
		} else {
			$certPathProd = $pemPathEnv;
			$envUpdates['EFI_CERT_PATH_PROD'] = $pemPathEnv;
			if ($originalName !== '') {
				$envUpdates['EFI_CERT_NAME_PROD'] = $originalName;
			}
		}
	}
}

processar_certificado_efi($_FILES['cert_p12_sandbox'] ?? [], $cert_password_sandbox, 'homolog', $envUpdates, $certPathProd, $certPathHomolog, $certDirBase);
processar_certificado_efi($_FILES['cert_p12_producao'] ?? [], $cert_password_producao, 'prod', $envUpdates, $certPathProd, $certPathHomolog, $certDirBase);

if (!empty($_FILES['efi_cert_p12']['tmp_name'])) {
	if (!function_exists('openssl_pkcs12_read')) {
		echo 'Extensao OpenSSL nao habilitada no PHP.';
		exit();
	}
	$p12Content = file_get_contents($_FILES['efi_cert_p12']['tmp_name']);
	$certs = [];
	if (!openssl_pkcs12_read($p12Content, $certs, $efi_cert_password)) {
		echo 'Falha ao ler o certificado .p12. Verifique a senha.';
		exit();
	}
	$certDir = $certDirBase !== '' ? $certDirBase : (dirname(__DIR__, 2) . '/efi_obp/certs');
	if (!is_dir($certDir)) {
		@mkdir($certDir, 0755, true);
	}
	$baseName = 'efi-cert-' . date('Y-m-d-H-i-s');
	$p12Path = $certDir . '/' . $baseName . '.p12';
	$pemPath = $certDir . '/' . $baseName . '.pem';
	$originalName = isset($_FILES['efi_cert_p12']['name']) ? basename((string)$_FILES['efi_cert_p12']['name']) : '';
	if (!move_uploaded_file($_FILES['efi_cert_p12']['tmp_name'], $p12Path)) {
		echo 'Falha ao salvar o certificado .p12.';
		exit();
	}
	$pemData = ($certs['cert'] ?? '') . "\n" . ($certs['pkey'] ?? '');
	if (!empty($certs['extracerts']) && is_array($certs['extracerts'])) {
		$pemData .= "\n" . implode("\n", $certs['extracerts']);
	}
	if (trim($pemData) === '') {
		echo 'Certificado .p12 invalido.';
		exit();
	}
	file_put_contents($pemPath, $pemData);
	$pemPathEnv = str_replace('\\', '/', $pemPath);
	if ($efi_cert_target === 'homolog') {
		$certPathHomolog = $pemPathEnv;
		$envUpdates['EFI_CERT_PATH_HOMOLOG'] = $pemPathEnv;
		if ($originalName !== '') {
			$envUpdates['EFI_CERT_NAME_HOMOLOG'] = $originalName;
		}
	} else {
		$certPathProd = $pemPathEnv;
		$envUpdates['EFI_CERT_PATH_PROD'] = $pemPathEnv;
		if ($originalName !== '') {
			$envUpdates['EFI_CERT_NAME_PROD'] = $originalName;
		}
	}
}

$envPath = $envBaseDir . '/.env';
if (!is_file($envPath)) {
	echo 'Arquivo .env do sistema alvo nao encontrado.';
	exit();
}
if (is_file($envPath)) {
	$envAtual = carregar_env_map($envPath);
	$oldCertHomolog = $envAtual['EFI_CERT_PATH_HOMOLOG'] ?? '';
	$oldCertProd = $envAtual['EFI_CERT_PATH_PROD'] ?? '';
	$newCertHomolog = $envUpdates['EFI_CERT_PATH_HOMOLOG'] ?? '';
	$newCertProd = $envUpdates['EFI_CERT_PATH_PROD'] ?? '';
	if ($newCertHomolog !== '') {
		limpar_certificado_antigo($oldCertHomolog, $newCertHomolog, $certDirBase);
	}
	if ($newCertProd !== '') {
		limpar_certificado_antigo($oldCertProd, $newCertProd, $certDirBase);
	}
}
if (!empty($envUpdates) && is_file($envPath)) {
	$lines = file($envPath, FILE_IGNORE_NEW_LINES);
	$updatedKeys = [];
	foreach ($lines as $idx => $line) {
		if (preg_match('/^\s*([A-Z0-9_]+)\s*=\s*(.*)$/', $line, $matches)) {
			$key = $matches[1];
			if (array_key_exists($key, $envUpdates)) {
				$lines[$idx] = $key . '=' . $envUpdates[$key];
				$updatedKeys[$key] = true;
			}
		}
	}
	foreach ($envUpdates as $key => $value) {
		if (!isset($updatedKeys[$key])) {
			$lines[] = $key . '=' . $value;
		}
	}
	file_put_contents($envPath, implode(PHP_EOL, $lines) . PHP_EOL);
}

function gateway_has_column(PDO $pdo, string $column) : bool {
	try {
		$stmt = $pdo->prepare("SHOW COLUMNS FROM gateways LIKE :col");
		$stmt->execute([':col' => $column]);
		return (bool)$stmt->fetch(PDO::FETCH_ASSOC);
	} catch (Exception $e) {
		return false;
	}
}

$gatewayHasCertPath = gateway_has_column($pdo, 'cert_path');

function upsert_gateway_efy(PDO $pdo, bool $gatewayHasCertPath, string $nome, string $clientId, string $clientSecret, string $webhookUrl, string $webhookPath, string $sandboxFlag, string $certPath = '') : void {
	$selCols = $gatewayHasCertPath ? "id, chave_api, chave_secreta, webhook_url, webhook_path, cert_path" : "id, chave_api, chave_secreta, webhook_url, webhook_path";
	$stmtSel = $pdo->prepare("SELECT $selCols FROM gateways WHERE nome = :nome LIMIT 1");
	$stmtSel->execute([':nome' => $nome]);
	$current = $stmtSel->fetch(PDO::FETCH_ASSOC);

	$newClientId = $clientId !== ''  $clientId : ($current['chave_api'] ?? '');
	$newClientSecret = $clientSecret !== ''  $clientSecret : ($current['chave_secreta'] ?? '');
	$newWebhookUrl = $webhookUrl !== ''  $webhookUrl : ($current['webhook_url'] ?? '');
	$newWebhookPath = $webhookPath !== ''  $webhookPath : ($current['webhook_path'] ?? '');
	$newCertPath = $certPath !== ''  $certPath : ($current['cert_path'] ?? '');

	if ($current) {
		$updateSql = "UPDATE gateways SET chave_api = :chave_api, chave_secreta = :chave_secreta, webhook_url = :webhook_url, webhook_path = :webhook_path, sandbox = :sandbox, ativo = 'Sim'";
		if ($gatewayHasCertPath) {
			$updateSql .= ", cert_path = :cert_path";
		}
		$updateSql .= " WHERE id = :id";
		$params = [':chave_api' => $newClientId, ':chave_secreta' => $newClientSecret, ':webhook_url' => $newWebhookUrl, ':webhook_path' => $newWebhookPath, ':sandbox' => $sandboxFlag, ':id' => (int)$current['id'],
		];
		if ($gatewayHasCertPath) {
			$params[':cert_path'] = $newCertPath;
		}
		$stmtUpd = $pdo->prepare($updateSql);
		$stmtUpd->execute($params);
		return;
	}

	$insertCols = "nome, chave_api, chave_secreta, webhook_url, webhook_path, ativo, data_cadastro, sandbox, gateway_path";
	$insertVals = ":nome, :chave_api, :chave_secreta, :webhook_url, :webhook_path, 'Sim', NOW(), :sandbox, :gateway_path";
	if ($gatewayHasCertPath) {
		$insertCols .= ", cert_path";
		$insertVals .= ", :cert_path";
	}
	$stmtIns = $pdo->prepare("INSERT INTO gateways ($insertCols) VALUES ($insertVals)");
	$params = [':nome' => $nome, ':chave_api' => $newClientId, ':chave_secreta' => $newClientSecret, ':webhook_url' => $newWebhookUrl, ':webhook_path' => $newWebhookPath, ':sandbox' => $sandboxFlag, ':gateway_path' => $newWebhookPath,
	];
	if ($gatewayHasCertPath) {
		$params[':cert_path'] = $newCertPath;
	}
	$stmtIns->execute($params);
} ? upsert_gateway_efy($pdo, $gatewayHasCertPath, 'EFY_PRODUCAO', $efi_client_id_prod, $efi_client_secret_prod, $efi_webhook_url_prod, $efi_webhook_path_prod, 'Nao', ($certPathProd : ($envUpdates['EFI_CERT_PATH_PROD'] ?? ''))); ? upsert_gateway_efy($pdo, $gatewayHasCertPath, 'EFY_HOMOLOG', $efi_client_id_homolog, $efi_client_secret_homolog, $efi_webhook_url_homolog, $efi_webhook_path_homolog, 'Sim', ($certPathHomolog : ($envUpdates['EFI_CERT_PATH_HOMOLOG'] ?? '')));


//atualizar os dados do config
$query = $pdo->prepare("UPDATE config SET nome_sistema = :nome_sistema, tel_sistema = :tel_sistema, email_sistema = :email_sistema, cnpj_sistema = :cnpj_sistema, tipo_chave_pix = :tipo_chave_pix, chave_pix = :chave_pix, logo = 'logo.png', icone = 'favicon.ico', logo_rel = 'logo_rel.jpg', qrcode_pix = 'qrcode.jpg', facebook = :facebook, instagram = :instagram, youtube = :youtube, itens_pag = :itens_pag, video_sobre = :video_sobre, aulas_liberadas = :aulas_liberadas, itens_relacionados = :itens_relacionados, desconto_pix = :desconto_pix, acrescimo_cartao_credito = :acrescimo_cartao_credito, email_adm_mat = :email_adm_mat, cartoes_fidelidade = :cartoes_fidelidade, taxa_mp = :taxa_mp, taxa_paypal = :taxa_paypal, taxa_boleto = :taxa_boleto, valor_max_cartao = :valor_max_cartao, total_emails_por_envio = :total_emails_por_envio, intervalo_envio_email = :intervalo_envio_email, dias_email_matricula = :dias_email_matricula, dias_excluir_matricula = :dias_excluir_matricula, professor_cad = :professor_cad, comissao_professor = :comissao_professor, dia_pgto_comissao = :dia_pgto_comissao, questionario = :questionario, media = :media, verso = :verso, api_cartao = :api_cartao, comissao_tesoureiro = :comissao_tesoureiro, comissao_secretario = :comissao_secretario, comissao_tutor = :comissao_tutor, comissao_parceiro = :comissao_parceiro, comissao_assessor = :comissao_assessor, comissao_vendedor = :comissao_vendedor");

$query->execute([':nome_sistema' => $nome_sistema, ':tel_sistema' => $tel_sistema, ':email_sistema' => $email_sistema, ':cnpj_sistema' => $cnpj_sistema, ':tipo_chave_pix' => $tipo_chave_pix, ':chave_pix' => $chave_pix, ':facebook' => $facebook_sistema, ':instagram' => $instagram_sistema, ':youtube' => $youtube_sistema, ':itens_pag' => $itens_pag, ':video_sobre' => $video_sobre, ':aulas_liberadas' => $aulas_lib, ':itens_relacionados' => $itens_rel, ':desconto_pix' => $desconto_pix, ':acrescimo_cartao_credito' => $acrescimo_cartao_credito, ':email_adm_mat' => $email_adm_mat, ':cartoes_fidelidade' => $cartoes_fidelidade, ':taxa_mp' => $taxa_mp, ':taxa_paypal' => $taxa_paypal, ':taxa_boleto' => $taxa_boleto, ':valor_max_cartao' => $valor_max_cartao, ':total_emails_por_envio' => $total_emails_por_envio, ':intervalo_envio_email' => $intervalo_envio_email, ':dias_email_matricula' => $dias_email_matricula, ':dias_excluir_matricula' => $dias_excluir_matricula, ':professor_cad' => $professor_cad, ':comissao_professor' => $comissao_professor, ':dia_pgto_comissao' => $dia_pgto_comissao, ':questionario' => $questionario, ':media' => $media, ':verso' => $verso, ':api_cartao' => $api_cartao, ':comissao_tesoureiro' => $comissao_tesoureiro, ':comissao_secretario' => $comissao_secretario, ':comissao_tutor' => $comissao_tutor, ':comissao_parceiro' => $comissao_parceiro, ':comissao_assessor' => $comissao_assessor, ':comissao_vendedor' => $comissao_vendedor,
]);

echo 'Editado com Sucesso';

?>