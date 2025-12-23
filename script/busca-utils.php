<?php
/**
 * Normaliza um texto para comparar sem acentos (minusculizado e sem diacríticos).
 */
function normalizar_busca($texto): string
{
    $texto = trim((string) $texto);
    if ($texto === '') {
        return '';
    }
    $texto = mb_strtolower($texto, 'UTF-8');
    $mapa = [
        'á' => 'a', 'à' => 'a', 'ã' => 'a', 'â' => 'a', 'ä' => 'a',
        'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
        'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i',
        'ó' => 'o', 'ò' => 'o', 'õ' => 'o', 'ô' => 'o', 'ö' => 'o',
        'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ü' => 'u',
        'ç' => 'c', 'ñ' => 'n',
    ];

    return strtr($texto, $mapa);
}

/**
 * Gera uma expressão SQL que normaliza um campo textual para comparação sem acentos.
 */
function sql_sem_acentos(string $campo): string
{
    $mapa = [
        'á' => 'a', 'à' => 'a', 'ã' => 'a', 'â' => 'a', 'ä' => 'a',
        'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
        'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i',
        'ó' => 'o', 'ò' => 'o', 'õ' => 'o', 'ô' => 'o', 'ö' => 'o',
        'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ü' => 'u',
        'ç' => 'c', 'ñ' => 'n',
    ];

    $expressao = "LOWER($campo)";
    foreach ($mapa as $caractere => $substituto) {
        $expressao = "REPLACE($expressao, '$caractere', '$substituto')";
    }

    return $expressao;
}

/**
 * Gera o padrão LIKE com o escape básico e o segundo padrão sem acentos.
 */
function preparar_padrao_busca(string $texto): array
{
    $texto = trim($texto);
    $padraoOriginal = '%' . addslashes($texto) . '%';
    $padraoSemAcento = '%' . addslashes(normalizar_busca($texto)) . '%';

    return [
        'original' => $padraoOriginal,
        'sem_acento' => $padraoSemAcento,
    ];
}

/**
 * Constrói uma cláusula SQL de busca sobre vários campos considerando acentos e collation.
 */
function clausula_busca_sem_acento(array $campos, string $padraoOriginal, string $padraoSemAcento): string
{
    $clausulas = [];
    foreach ($campos as $campo) {
        $clausulas[] = "$campo LIKE '$padraoOriginal' COLLATE utf8_general_ci";
        $clausulas[] = sql_sem_acentos($campo) . " LIKE '$padraoSemAcento'";
    }

    return '(' . implode(' OR ', $clausulas) . ')';
}
