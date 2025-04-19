<?php

namespace Hejunjie\UrlSigner;

class Helpers
{
    /**
     * 生成随机字符串
     * 
     * @param int $length 字符串长度
     * 
     * @return string
     */
    public static function generateRandomStringgenerateRandomString(int $len = 32, bool $string = true, bool $int = true): string
    {
        $str = '';
        if ($string) {
            $str .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        }
        if ($int) {
            $str .= '0123456789';
        }
        if (empty($str)) {
            return ''; // 如果不添加字母和数字，返回空字符串
        }
        $noncestr = '';
        for ($i = 0; $i < $len; $i++) {
            $noncestr .= substr($str, mt_rand(0, strlen($str) - 1), 1);
        }
        return $noncestr;
    }

    /**
     * 生成签名
     * 
     * @param array $params 加密参数
     * @param string $secretKey 加密key
     * 
     * @return string 
     */
    public static function generateSignature(array $params, string $secretKey): string
    {
        ksort($params);
        return hash_hmac('sha256', http_build_query($params), $secretKey);
    }
}
