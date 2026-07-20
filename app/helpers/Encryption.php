<?php
namespace App\Helpers;

class Encryption {
    /**
     * Encrypt data using AES-256-CBC
     */
    public static function encrypt(string $data): string {
        $cipher = ENCRYPTION_CIPHER;
        $key = ENCRYPTION_KEY;
        
        $ivlen = openssl_cipher_iv_length($cipher);
        $iv = random_bytes($ivlen);
        
        $ciphertextRaw = openssl_encrypt($data, $cipher, $key, OPENSSL_RAW_DATA, $iv);
        
        // Return base64 containing the IV concatenated with ciphertext
        return base64_encode($iv . $ciphertextRaw);
    }

    /**
     * Decrypt data using AES-256-CBC
     */
    public static function decrypt(string $data): string {
        $cipher = ENCRYPTION_CIPHER;
        $key = ENCRYPTION_KEY;
        
        $raw = base64_decode($data);
        $ivlen = openssl_cipher_iv_length($cipher);
        
        if (strlen($raw) <= $ivlen) {
            return '';
        }
        
        $iv = substr($raw, 0, $ivlen);
        $ciphertextRaw = substr($raw, $ivlen);
        
        $decrypted = openssl_decrypt($ciphertextRaw, $cipher, $key, OPENSSL_RAW_DATA, $iv);
        
        return $decrypted !== false ? $decrypted : '';
    }
}
