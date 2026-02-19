<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/boleto.php';

 = require __DIR__ . '/efi/options.php';

 = ['clientId'];
 = ['clientSecret'];
 = ['sandbox'];

 = new EFIBoletoPayment(, , );

 = [
    'valor' => 1000,
    'item_nome' => 'Teste Boleto',
    'quantidade' => 1,
    'nome' => 'Test User',
    'email' => 'test@example.com',
    'cpf' => '12345678909',
    'telefone' => '69999999999',
    'notification_url' => 'https://sestedcursosvirtual.com/efi_webhook_boleto.php',
    'repasses' => []
];

try {
     = ->createBoletoCharge();
    echo  Success:\n;
    print_r();
} catch (Throwable ) {
    echo Erro:  . ->getMessage() .  \n;
}
