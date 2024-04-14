<?php

/**
 * Fetch the current price for a cryptocurrency using CoinGecko API.
 *
 * @param string $cryptoId The ID of the cryptocurrency (e.g., 'bitcoin').
 * @return float|null Current price in USD, or null if not available.
 */
function fetchCryptoPrice(string $cryptoId): ?float {
    $url = "https://api.coingecko.com/api/v3/simple/price?ids={$cryptoId}&vs_currencies=usd";

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    curl_close($curl);

    $data = json_decode($response, true);

    // If the API returns a valid response, $data[$cryptoId]['usd'] will be a float or integer
    // If not found, null is returned
    // The return type is declared as ?float, which means this function can return a float or null
    return isset($data[$cryptoId]['usd']) ? (float)$data[$cryptoId]['usd'] : null;
}


function fetchStockPrice($symbol) {
    $apiKey = 'OHg2z4ngsMJqmR6wKJejvyiNkt1aHTfI';
    $url = "https://financialmodelingprep.com/api/v3/quote/{$symbol}?apikey={$apiKey}";

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    curl_close($curl);

    $data = json_decode($response, true);

    if (!empty($data) && is_array($data)) {
        return $data[0]['price'] ?? null;
    }

    return null;
}

