<?php

class EFICreditCardPayment
{
    private $clientId;
    private $clientSecret;
    private $sandbox;
    private $baseUrl;

    public function __construct($clientId, $clientSecret, $sandbox = false)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->sandbox = $sandbox;
        $this->baseUrl = $sandbox ? 'https://sandbox.gerencianet.com.br' : 'https://api.gerencianet.com.br';
    }

    private function getAccessToken()
    {
        $url = $this->baseUrl . '/v1/authorize';

        $postData = json_encode([
            'grant_type' => 'client_credentials'
        ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret)
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_error($ch)) {
            throw new Exception('Erro cURL: ' . curl_error($ch));
        }

        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception('Erro na autenticação Cartão: ' . $response);
        }

        $data = json_decode($response, true);
        return $data['access_token'] ?? null;
    }

    private function requestApi(string $method, string $path, string $token, ?array $body = null): array
    {
        $url = $this->baseUrl . $path;
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

        if ($body !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_error($ch)) {
            throw new Exception('Erro cURL: ' . curl_error($ch));
        }

        curl_close($ch);

        $decoded = json_decode((string) $response, true);
        return [
            'status' => (int) $httpCode,
            'body' => is_array($decoded) ? $decoded : [],
            'raw' => (string) $response
        ];
    }

    public function createCreditCardCharge($dados)
    {
        $token = $this->getAccessToken();

        if (!$token) {
            throw new Exception('Não foi possível obter o token de acesso');
        }

        // Criar a cobrança
        $chargeId = $this->createCharge($dados, $token);

        // Definir pagamento via cartão
        $paymentData = $this->payWithCreditCard($chargeId, $dados, $token);

        return $paymentData;
    }

    public function createRecurringSubscription(array $dados): array
    {
        $token = $this->getAccessToken();
        if (!$token) {
            throw new Exception('Nao foi possivel obter o token de acesso');
        }

        $repeats = (int) ($dados['installments'] ?? 1);
        if ($repeats < 2) {
            $repeats = 2;
        }

        $itemName = $dados['item_nome'] ?? 'Produto/Servico';
        $itemValue = (int) round(((float) ($dados['valor'] ?? 0)) * 100);
        $itemAmount = (int) ($dados['quantidade'] ?? 1);

        if ($itemValue <= 0) {
            throw new Exception('Valor da assinatura invalido.');
        }

        $items = [[
            'name' => $itemName,
            'value' => $itemValue,
            'amount' => $itemAmount
        ]];

        // A API de assinatura (one-step) não aceita marketplace em items.
        // O split permanece no fluxo de cobrança avulsa com cartão.

        $planPayload = [
            'name' => 'Plano ' . substr((string) $itemName, 0, 120),
            'interval' => 1,
            'repeats' => $repeats
        ];

        $planResp = $this->requestApi('POST', '/v1/plan', $token, $planPayload);
        if (!in_array($planResp['status'], [200, 201], true)) {
            throw new Exception('Erro ao criar plano recorrente: ' . $planResp['raw']);
        }

        $planId = $planResp['body']['data']['plan_id'] ?? null;
        if (!$planId) {
            throw new Exception('Plan_id nao retornado pela EFI.');
        }

        $subscriptionPayload = [
            'items' => $items,
            'metadata' => [
                'notification_url' => $dados['notification_url'] ?? null
            ],
            'payment' => [
                'credit_card' => [
                    'billing_address' => [
                        'street' => $dados['street'],
                        'number' => $dados['number'],
                        'neighborhood' => $dados['neighborhood'],
                        'zipcode' => preg_replace('/\D/', '', (string) $dados['zipcode']),
                        'city' => $dados['city'],
                        'state' => strtoupper((string) $dados['state'])
                    ],
                    'customer' => [
                        'name' => $dados['nome'],
                        'email' => $dados['email'],
                        'cpf' => preg_replace('/\D/', '', (string) $dados['cpf']),
                        'birth' => '1995-10-27',
                        'phone_number' => preg_replace('/\D/', '', (string) ($dados['telefone'] ?? '11999999999'))
                    ],
                    'payment_token' => $dados['credit_card_token']
                ]
            ]
        ];

        if (empty($subscriptionPayload['metadata']['notification_url'])) {
            unset($subscriptionPayload['metadata']);
        }

        $subResp = $this->requestApi('POST', '/v1/plan/' . $planId . '/subscription/one-step', $token, $subscriptionPayload);
        if (!in_array($subResp['status'], [200, 201], true)) {
            throw new Exception('Erro ao criar assinatura recorrente: ' . $subResp['raw']);
        }

        $subData = $subResp['body']['data'] ?? [];
        $subscriptionId = $subData['subscription_id'] ?? null;
        $chargeId = $subData['charge']['charge_id'] ?? ($subData['charge_id'] ?? null);
        $total = isset($subData['charge']['total']) ? ((float) $subData['charge']['total'] / 100) : ((float) ($dados['valor'] ?? 0));
        $status = $subData['status'] ?? ($subData['charge']['status'] ?? 'new');

        return [
            'plan_id' => $planId,
            'subscription_id' => $subscriptionId,
            'charge_id' => $chargeId,
            'status' => $status,
            'total' => $total,
            'payment_data' => $subData
        ];
    }

    private function createCharge($dados, $token)
    {
        $url = $this->baseUrl . '/v1/charge';

        $items = [];
        if (isset($dados['items']) && is_array($dados['items'])) {
            $items = $dados['items'];
        } else {
            $item = [
                'name' => $dados['item_nome'] ?? 'Produto/Servico',
                'value' => (int) ($dados['valor'] * 100),
                'amount' => $dados['quantidade'] ?? 1
            ];
            if (!empty($dados['repasses'])) {
                $item['marketplace'] = [
                    'repasses' => $dados['repasses']
                ];
            }
            $items[] = $item;
        }

        $metadata = ['notification_url' => $dados['notification_url'] ?? null];
        $body = [
            'items' => $items,
            'metadata' => $metadata
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_error($ch)) {
            throw new Exception('Erro cURL: ' . curl_error($ch));
        }

        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception('Erro ao criar cobrança: ' . $response);
        }

        $data = json_decode($response, true);
        return $data['data']['charge_id'] ?? null;
    }

    private function payWithCreditCard($chargeId, $dados, $token)
    {
        $url = $this->baseUrl . '/v1/charge/' . $chargeId . '/pay';
        $zipcode = preg_replace('/\D/', '', (string) ($dados['zipcode'] ?? ''));
        $number = trim((string) ($dados['number'] ?? ''));
        $street = trim((string) ($dados['street'] ?? ''));
        $neighborhood = trim((string) ($dados['neighborhood'] ?? ''));
        $city = trim((string) ($dados['city'] ?? ''));
        $state = strtoupper(trim((string) ($dados['state'] ?? '')));

        $body = [
            'payment' => [
                'credit_card' => [
                    'installments' => (int) ($dados['installments'] ?? 1),
                    'billing_address' => [
                        'street' => $street,
                        'number' => $number,
                        'neighborhood' => $neighborhood,
                        'zipcode' => $zipcode,
                        'city' => $city,
                        'state' => $state
                    ],
                    'customer' => [
                        'name' => $dados['nome'],
                        'email' => $dados['email'],
                        'cpf' => preg_replace('/\D/', '', $dados['cpf']),
                        'birth' => '1995-10-27',
                        'phone_number' => '11961722303'
                    ],
                    'payment_token' => $dados['credit_card_token'] // gerado pelo JS SDK da Gerencianet
                ]
            ]
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_error($ch)) {
            throw new Exception('Erro cURL: ' . curl_error($ch));
        }

        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception('Erro ao processar pagamento com cartão: ' . $response);
        }

        $data = json_decode($response, true);

        return [
            'charge_id' => $chargeId,
            'status' => $data['data']['status'] ?? 'waiting',
            'total' => $data['data']['total'] / 100,
            'payment_data' => $data['data']
        ];
    }

    public function consultarCobranca($chargeId)
    {
        $token = $this->getAccessToken();
        $url = $this->baseUrl . '/v1/charge/' . $chargeId;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization' => 'Bearer ' . $token
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            return json_decode($response, true);
        }

        throw new Exception('Erro ao consultar cobrança: ' . $response);
    }
}

?>
