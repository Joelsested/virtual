<?php

/**
 * Environment
 */
$sandbox = false; // false = Production | true = Homologation

/**
 * Credentials of Production
 */
$clientIdProd = "Client_Id_85303d1fb0ee3e438d371d08bacd19dfdbc9074a";
$clientSecretProd = "Client_Secret_8591be09d2f43bd0e99e43ae1dfa74201ead44eb";
$pathCertificateProd = __DIR__ . '/producao-290086-cursos_livres_cert.pem'; // Absolute path to the certificate in .pem or .p12 format

/**
 * Credentials of Homologation
 */
$clientIdHomolog = "Client_Id_c82c9fda8c8b8d77f0289473d05d92e79c6fd021";
$clientSecretHomolog = "Client_Secret_c2c702d53046881c37b703b7c45860b6b8307ff5";
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
