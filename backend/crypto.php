<?php

    require '../assests/css/css_config.php';
function encrypt($data, $key) {
    if (strlen($key) !== 32) {
        throw new Exception("Invalid key size.");
    }
    $iv = random_bytes(openssl_cipher_iv_length('aes-256-cbc'));
    $encrypted = openssl_encrypt($data, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
    return base64_encode($iv . $encrypted);
}

function decrypt($encryptedData, $key) {
    if (empty($encryptedData) || strlen($key) !== 32) {
        throw new InvalidArgumentException('Invalid input parameters for decryption');
    }
    $raw = base64_decode($encryptedData);
    if ($raw === false) {
        throw new RuntimeException('Base64 decoding failed');
    }
    $ivlen = openssl_cipher_iv_length('aes-256-cbc');
    if ($ivlen === false) {
        throw new RuntimeException('Could not determine IV length');
    }
    $iv = substr($raw, 0, $ivlen);
    $ciphertext = substr($raw, $ivlen);
    $decrypted = openssl_decrypt($ciphertext,'aes-256-cbc',$key,OPENSSL_RAW_DATA,$iv);

    if ($decrypted === false) {
        throw new RuntimeException('Decryption failed: ' . openssl_error_string());
    }
    return $decrypted;
}

$original_data = 'Sensitive';
$encrypted_data = encrypt($original_data, ENCRYPTION_KEY);
$decrypted_data = decrypt($encrypted_data, ENCRYPTION_KEY);

?>