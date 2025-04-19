<?php

namespace Hejunjie\UrlSigner;

class Installer
{
    /**
     * 生成密钥
     * 
     * @return void 
     */
    public static function generateKey(): array
    {
        $configDir = __DIR__ . '/../config';
        $configFile = $configDir . '/urlsigner.php';
        // 确保 config 目录存在
        if (!is_dir($configDir)) {
            mkdir($configDir, 0755, true);
        }
        // 如果已有密钥，则返回
        if (file_exists($configFile)) {
            $config = include $configFile;
            if (!empty($config['secretKey'])) {
                return $config;
            }
        }
        // 生成一个 32 字符长度的随机密钥
        $key = bin2hex(random_bytes(16)); // 16 字节 = 32 十六进制字符
        // 写入配置文件
        $content = <<<PHP
<?php

return [
    'secretKey' => '{$key}',
    'default_expire' => 3600, // 默认签名有效期，单位：秒
];
PHP;

        file_put_contents($configFile, $content);
        $config = include $configFile;
        if (!empty($config['secretKey'])) {
            return $config;
        }
        throw new \Exception('配置文件生成失败，请手动传入配置信息');
    }
}
