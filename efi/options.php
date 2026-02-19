<?php

require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/../sistema/conexao.php';

/**
 * Environment
 */
$sandbox = filter_var(env('EFI_SANDBOX', 'false'), FILTER_VALIDATE_BOOLEAN); // false = Production | true = Homologation

/**
 * Credentials of Production
 */
$clientIdProd = env('EFI_CLIENT_ID_PROD', '');
$clientSecretProd = env('EFI_CLIENT_SECRET_PROD', '');
$pathCertificateProd = env('EFI_CERT_PATH_PROD', __DIR__ . '/producao-517293-SESTED-EJA_cert.pem'); // Absolute path to the certificate in .pem or .p12 format

/**
 * Credentials of Homologation
 */
$clientIdHomolog = env('EFI_CLIENT_ID_HOMOLOG', '');
$clientSecretHomolog = env('EFI_CLIENT_SECRET_HOMOLOG', '');
$pathCertificateHomolog = env('EFI_CERT_PATH_HOMOLOG', __DIR__ . '/homologacao-517293-SESTED-EJA-HOMO_cert.pem'); // Absolute path to the certificate in .pem or .p12 format

$pixKey = env('EFI_PIX_KEY', '');

$dbProd = ['chave_api' => '', 'chave_secreta' => ''];
$dbHml = ['chave_api' => '', 'chave_secreta' => ''];
try {
	$stmt = $pdo->query("SELECT nome, chave_api, chave_secreta FROM gateways WHERE UPPER(nome) IN ('EFY_PRODUCAO','EFI_PRODUCAO','EFY_HOMOLOG','EFI_HOMOLOG','EFY','EFI') ORDER BY id ASC");
	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	foreach ($rows as $row) {
		$nome = strtoupper((string)($row['nome'] ?? ''));
		if (strpos($nome, 'HOMOLOG') !== false) {
			$dbHml = $row;
			continue;
		}
		if (strpos($nome, 'PRODUCAO') !== false) {
			$dbProd = $row;
			continue;
		}
		if (($dbProd['chave_api'] ?? '') === '' && ($dbProd['chave_secreta'] ?? '') === '') {
			$dbProd = $row;
		} else {
			$dbHml = $row;
		}
	}
} catch (Exception $e) {
	// Fallback para .env.
}

$clientIdProd = ($dbProd['chave_api'] ?? '') !== '' ? (string)$dbProd['chave_api'] : $clientIdProd;
$clientSecretProd = ($dbProd['chave_secreta'] ?? '') !== '' ? (string)$dbProd['chave_secreta'] : $clientSecretProd;
$clientIdHomolog = ($dbHml['chave_api'] ?? '') !== '' ? (string)$dbHml['chave_api'] : $clientIdHomolog;
$clientSecretHomolog = ($dbHml['chave_secreta'] ?? '') !== '' ? (string)$dbHml['chave_secreta'] : $clientSecretHomolog;

/**
 * Array with credentials and other settings
 */
return [
	"clientId" => ($sandbox) ? $clientIdHomolog : $clientIdProd,
	"clientSecret" => ($sandbox) ? $clientSecretHomolog : $clientSecretProd,
	"certificate" => ($sandbox) ? $pathCertificateHomolog : $pathCertificateProd,
	"pwdCertificate" => ($sandbox) ? $pathCertificateHomolog : $pathCertificateProd, // Optional | Default = ""
	"sandbox" => $sandbox, // Optional | Default = false
	"pixKey" => $pixKey,
	"debug" => false, // Optional | Default = false
	"timeout" => 30, // Optional | Default = 30
	"responseHeaders" => true, //  Optional | Default = false
];
