<?php

/**
 * Environment
 */
$sandbox = false; // false = Production | true = Homologation

/**
 * Credentials of Production
 */
$clientIdProd = "Client_Id_aeaff512b0a48d097ecc4e5077ead5bec718f925";
$clientSecretProd = "Client_Secret_cc62eaa288a65755380191079f9197fc5bf28bd8";
$pathCertificateProd = __DIR__ . '/producao-290086-cursos_livres_cert.pem'; // Absolute path to the certificate in .pem or .p12 format

/**
 * Credentials of Homologation
 */
$clientIdHomolog = "Client_Id_65394947f790cdec51f1aefd7c4d8c0eb93511f8";
$clientSecretHomolog = "Client_Secret_0b46e745b2a37ce7fbf18dfeda753a95e488760c";
$pathCertificateHomolog = __DIR__ . '/homologacao-290086-cursos_livres_cert.pem'; // Absolute path to the certificate in .pem or .p12 format

/**
 * Array with credentials and other settings
 */
return [
	"clientId" => ($sandbox) ? $clientIdHomolog : $clientIdProd,
	"clientSecret" => ($sandbox) ? $clientSecretHomolog : $clientSecretProd,
	"certificate" => ($sandbox) ? $pathCertificateHomolog : $pathCertificateProd,
	"pwdCertificate" => ($sandbox) ? $pathCertificateHomolog : $pathCertificateProd, // Optional | Default = ""
	"sandbox" => $sandbox, // Optional | Default = false
	"debug" => false, // Optional | Default = false
	"timeout" => 30, // Optional | Default = 30
	"responseHeaders" => true, //  Optional | Default = false
];
