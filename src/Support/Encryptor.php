<?php

namespace Hejunjie\UrlSigner\Support;

class Encryptor
{
    protected const METHOD = 'AES-256-CBC';

    /**
     * AES 加密
     * 
     * @param string $data 需要加密的数据
     * @param string $key 加密key
     * 
     * @return string 
     */
    public static function encrypt(string $data, string $key): string
    {
        $key = hash('sha256', $key, true);
        $iv = substr($key, 0, 16);
        $encrypted = openssl_encrypt($data, self::METHOD, $key, OPENSSL_RAW_DATA, $iv);
        return base64_encode($encrypted);
    }

    /**
     * AES 解密
     * 
     * @param string $encrypted 需要解密的数据
     * @param string $key 加密key
     * 
     * @return string 
     */
    public static function decrypt(string $encrypted, string $key): string
    {
        $key = hash('sha256', $key, true);
        $iv = substr($key, 0, 16);
        $data = base64_decode($encrypted);
        return openssl_decrypt($data, self::METHOD, $key, OPENSSL_RAW_DATA, $iv);
    }
}
