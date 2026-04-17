<?php

require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/../sistema/conexao.php';

function efiCipherKey(): string
{
    $key = env('APP_KEY', 'sested-default-key-32chars!!');
    return substr(hash('sha256', $key, true), 0, 32);
}

function efiDecryptValue(?string $encoded): string
{
    if ($encoded === null || $encoded === '') {
        return '';
    }

    $raw = base64_decode($encoded, true);
    if ($raw === false || strlen($raw) < 17) {
        return '';
    }

    $iv = substr($raw, 0, 16);
    $cipher = substr($raw, 16);
    $plain = openssl_decrypt($cipher, 'AES-256-CBC', efiCipherKey(), OPENSSL_RAW_DATA, $iv);
    return $plain === false ? '' : $plain;
}

function efiBoolFromString($value, bool $default = false): bool
{
    if ($value === null) {
        return $default;
    }
    $normalized = strtolower(trim((string) $value));
    if ($normalized === '') {
        return $default;
    }
    return in_array($normalized, ['1', 'true', 'yes', 'on', 'sim', 'sandbox'], true);
}

$sandbox = efiBoolFromString(env('EFI_SANDBOX', 'false'));
$clientIdProd = (string) env('EFI_CLIENT_ID_PROD', '');
$clientSecretProd = (string) env('EFI_CLIENT_SECRET_PROD', '');
$pathCertificateProd = (string) env('EFI_CERT_PATH_PROD', __DIR__ . '/producao-517293-SESTED-EJA_cert.pem');

$clientIdHomolog = (string) env('EFI_CLIENT_ID_HOMOLOG', '');
$clientSecretHomolog = (string) env('EFI_CLIENT_SECRET_HOMOLOG', '');
$pathCertificateHomolog = (string) env('EFI_CERT_PATH_HOMOLOG', __DIR__ . '/homologacao-517293-SESTED-EJA-HOMO_cert.pem');

$pwdCertificateProd = (string) env('EFI_CERT_PASSWORD_PROD', '');
$pwdCertificateHomolog = (string) env('EFI_CERT_PASSWORD_HOMOLOG', '');
$notificationUrl = (string) env('EFI_WEBHOOK_URL', '');
$webhookPath = (string) env('EFI_WEBHOOK_PATH', '');
$pixKey = (string) env('EFI_PIX_KEY', (string) ($GLOBALS['chave_pix'] ?? ''));

if (isset($pdo) && $pdo instanceof PDO) {
    try {
        $pdo->exec("UPDATE gateways SET provider = 'efy' WHERE (provider IS NULL OR provider = '') AND UPPER(COALESCE(nome, '')) IN ('EFY', 'EFI')");
        $pdo->exec("UPDATE gateways SET ambiente = CASE WHEN LOWER(COALESCE(sandbox, '')) IN ('sim', '1', 'true', 'sandbox') THEN 'sandbox' ELSE 'producao' END WHERE provider = 'efy' AND (ambiente IS NULL OR ambiente = '')");

        $stmt = $pdo->prepare("SELECT * FROM gateways WHERE provider = 'efy' AND sandbox = 'Sim' ORDER BY updated_at DESC, id DESC LIMIT 1");
        $stmt->execute();
        $gatewayAtivo = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($gatewayAtivo) {
            $ambienteAtivo = strtolower((string) ($gatewayAtivo['ambiente'] ?? ''));
            if ($ambienteAtivo === 'sandbox') {
                $sandbox = true;
            } elseif ($ambienteAtivo === 'producao') {
                $sandbox = false;
            } else {
                $sandbox = efiBoolFromString($gatewayAtivo['sandbox'] ?? '', false);
            }

            $clientIdDb = efiDecryptValue($gatewayAtivo['chave_api'] ?? '');
            $clientSecretDb = efiDecryptValue($gatewayAtivo['chave_secreta'] ?? '');
            $certPathDb = trim((string) ($gatewayAtivo['cert_path'] ?? ''));
            $certPasswordDb = efiDecryptValue($gatewayAtivo['cert_password'] ?? '');
            $webhookUrlDb = trim((string) ($gatewayAtivo['webhook_url'] ?? ''));
            $webhookPathDb = trim((string) ($gatewayAtivo['webhook_path'] ?? ''));

            if ($sandbox) {
                if ($clientIdDb !== '') {
                    $clientIdHomolog = $clientIdDb;
                }
                if ($clientSecretDb !== '') {
                    $clientSecretHomolog = $clientSecretDb;
                }
                if ($certPathDb !== '') {
                    $pathCertificateHomolog = $certPathDb;
                }
                if ($certPasswordDb !== '') {
                    $pwdCertificateHomolog = $certPasswordDb;
                }
            } else {
                if ($clientIdDb !== '') {
                    $clientIdProd = $clientIdDb;
                }
                if ($clientSecretDb !== '') {
                    $clientSecretProd = $clientSecretDb;
                }
                if ($certPathDb !== '') {
                    $pathCertificateProd = $certPathDb;
                }
                if ($certPasswordDb !== '') {
                    $pwdCertificateProd = $certPasswordDb;
                }
            }

            if ($webhookUrlDb !== '') {
                $notificationUrl = $webhookUrlDb;
            }
            if ($webhookPathDb !== '') {
                $webhookPath = $webhookPathDb;
            }
        }
    } catch (Throwable $e) {
        // Mantem fallback de ambiente via .env se houver erro de leitura no banco.
    }
}

$clientId = $sandbox ? $clientIdHomolog : $clientIdProd;
$clientSecret = $sandbox ? $clientSecretHomolog : $clientSecretProd;
$certificatePath = $sandbox ? $pathCertificateHomolog : $pathCertificateProd;
$pwdCertificate = $sandbox ? $pwdCertificateHomolog : $pwdCertificateProd;

return [
    'clientId' => $clientId,
    'clientSecret' => $clientSecret,
    'certificate' => $certificatePath,
    'pwdCertificate' => $pwdCertificate,
    'sandbox' => $sandbox,
    'pixKey' => $pixKey,
    'notificationUrl' => $notificationUrl,
    'webhookPath' => $webhookPath,
    'baseUrlPix' => $sandbox ? 'https://api-pix-h.gerencianet.com.br' : 'https://api-pix.gerencianet.com.br',
    'baseUrlBoleto' => $sandbox ? 'https://cobrancas-h.api.efipay.com.br' : 'https://cobrancas.api.efipay.com.br',
    'debug' => false,
    'timeout' => 30,
    'responseHeaders' => true,
];
