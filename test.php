<?php

require __DIR__ . '/vendor/autoload.php';

use Hejunjie\UrlSigner\UrlSigner;

$UrlSigner = new UrlSigner();

$url = 'https://www.baidu.com/fileDownload';
$path = '/home/wwwroot/file/test.zip';

// $download_url = $UrlSigner->sign($url, $path);

// echo '[加密链接]'.$download_url.PHP_EOL;

echo '[urldecode]' . urldecode('%2BhD8kmUOqGEyF5ghLHKQUlaDL%2F1j3Dw5AdPE2U%2FpMQM%3D') . PHP_EOL;

var_dump($UrlSigner->decryptFileParam(urldecode('%2BhD8kmUOqGEyF5ghLHKQUlaDL%2F1j3Dw5AdPE2U%2FpMQM%3D')));
