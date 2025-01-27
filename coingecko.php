<?php
date_default_timezone_set('Asia/Bangkok');  // This sets GMT+7 timezone
// Include database connection
require 'includes/db.php';
require 'config/config.php';
// Function to fetch cryptocurrency rates from CoinGecko
function getCryptoRatesFromCoinGecko() {
    $url = 'https://api.coingecko.com/api/v3/simple/price?ids=bitcoin,ethereum,dogecoin,litecoin,bitcoin-cash,dash,digibyte,tron,tether,feyorra,zcash,binancecoin,solana,ripple,matic-network,cardano,the-open-network,stellar,usd-coin,monero,taraxa&vs_currencies=usd';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

// Function to update cryptocurrency rates in the database
function updateCryptoRatesInDB($pdo, $crypto_rates) {
    $stmt = $pdo->prepare("
        INSERT INTO crypto_rates (crypto_name, rate_usd, last_updated)
        VALUES (:crypto_name, :rate_usd, NOW())
        ON DUPLICATE KEY UPDATE rate_usd = :rate_usd, last_updated = NOW()
    ");
    
    foreach ($crypto_rates as $crypto_name => $data) {
        $stmt->execute([
            'crypto_name' => $crypto_name,
            'rate_usd' => $data['usd'],
        ]);
    }
}

// Fetch cryptocurrency rates from CoinGecko
$crypto_rates = getCryptoRatesFromCoinGecko();

// Update rates in the database
if ($crypto_rates) {
    updateCryptoRatesInDB($pdo, $crypto_rates);
    echo "Crypto rates updated successfully.";
} else {
    echo "Failed to fetch crypto rates from CoinGecko.";
}
?>