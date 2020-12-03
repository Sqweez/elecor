<?php


namespace App\Http\Controllers\Service;


class CryptService {
    protected $ciphering = 'AES-128-CTR';
    protected $options = 0;
    protected $encryption_iv;
    protected $encryption_key;

    public function __construct() {
        $this->encryption_iv = env('CRYPT_IV');
        $this->encryption_key = env('CRYPT_KEY');
    }

    public function encryptString($string) {
        // Добавил base_64, потому что реф передаваться будет гетом, чтобы исключить символы запрещенные в гете
        return base64_encode(openssl_encrypt($string, $this->ciphering,
            $this->encryption_key, $this->options, $this->encryption_iv));
    }

    public function decryptString($encryptedString) {
        // Добавил base_64, потому что реф передаваться будет гетом, чтобы исключить символы запрещенные в гете
        return openssl_decrypt (base64_decode($encryptedString), $this->ciphering,
            $this->encryption_key, $this->options, $this->encryption_iv);
    }
}
