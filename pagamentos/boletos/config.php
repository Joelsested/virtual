<?php
require_once __DIR__ . '/../../config/env.php';

$sandbox = filter_var(env('EFI_SANDBOX', 'false'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
if ($sandbox === null) {
    $sandbox = false;
}

if ($sandbox) {
    $clientId = env('EFI_CLIENT_ID_HOMOLOG', '');
    $clientSecret = env('EFI_CLIENT_SECRET_HOMOLOG', '');
} else {
    $clientId = env('EFI_CLIENT_ID_PROD', '');
    $clientSecret = env('EFI_CLIENT_SECRET_PROD', '');
}

define('CONF_ID', $clientId);
define('CONF_SECRETO', $clientSecret);
define('CONF_SANDBOX', $sandbox); // true = homologa??o, false = produ??o

?>
