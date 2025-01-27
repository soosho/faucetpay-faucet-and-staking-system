<?php
// includes/faucetpay_api.php

function faucetpay_api_request($endpoint, $params = []) {
    $config = require('config/config.php');
    $params['api_key'] = $config['faucetpay']['api_key'];
    
    // Build the query string
    $context_options = [
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/x-www-form-urlencoded',
            'content' => http_build_query($params),
        ]
    ];

    // Make the API request
    $context = stream_context_create($context_options);
    $response = file_get_contents('https://faucetpay.io/api/v1/' . $endpoint, false, $context);
    
    if ($response === FALSE) {
        die('Error contacting FaucetPay API');
    }

    // Debugging output: See exactly what the API returns
    echo '<pre>';
    print_r($response);
    echo '</pre>';

    return json_decode($response, true);
}

/**
 * Send a payment to a FaucetPay user
 * @param string $to - The recipient FaucetPay email
 * @param float $amount - The amount to send
 * @param string $currency - The currency to send (e.g., BTC, DOGE, etc.)
 */
function send_payment($to, $amount, $currency = 'BTC') {
    return faucetpay_api_request('send', [
        'to' => $to,
        'amount' => $amount,
        'currency' => $currency
    ]);
}

/**
 * Get the balance of a specific currency
 * @param string $currency - The currency to check balance for (e.g., BTC, DOGE)
 */
function get_faucetpay_balance($currency = 'BTC') {
    return faucetpay_api_request('balance', [
        'currency' => $currency
    ]);
}

/**
 * Get a list of all balances for all supported cryptocurrencies
 */
function get_all_faucetpay_balances() {
    return faucetpay_api_request('balances');
}

/**
 * Get the deposit status for a specific transaction
 * @param string $txid - The transaction ID
 */
function get_deposit_status($txid) {
    return faucetpay_api_request('checkdeposit', ['txid' => $txid]);
}
