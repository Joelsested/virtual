<?php

function makeRequest($url, $data = null, $method = 'POST')
{
    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json, */*'
        ]
    ]);

    if ($method === 'POST' && $data) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

    if (curl_errno($ch)) {
        throw new Exception('Erro na requisição: ' . curl_error($ch));
    }

    curl_close($ch);

    if ($http_code !== 200) {
        throw new Exception("Erro HTTP: $http_code - Response: $response");
    }

    if (stripos($content_type, 'application/json') !== false) {
        return json_decode($response, true);
    }

    return $response;
}

try {
    $url = "https://partner.conted.tech/api/contents-all";
    $data = ['integration_key' => 'SDf8MccOO1Bkbb6dlCs2RI1IULHLnLw0yHExZkQlO5CkY31qOuHdDyYySosFc6ng', 'updated' => '2025-09-04'];
    $result = makeRequest($url, $data, 'POST');

    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'success',
        'data' => $result
    ]);

} catch (Exception $e) {
    header('Content-Type: application/json', true, 500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
