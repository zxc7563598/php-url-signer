<?php

namespace Hejunjie\UrlSigner;

use Hejunjie\UrlSigner\Support\Encryptor;

class UrlSigner
{
    protected string $secretKey;
    protected int $defaultExpire;

    /**
     * 构造函数
     * 
     * @param array $config 配置信息，可不传 ['secretKey'=>'加密key（32字符长度）','default_expire'=>'默认有效期']
     * 
     * @return void 
     */
    public function __construct(array $config = [])
    {
        $config = array_merge([
            'secretKey' => '',
            'default_expire' => 3600,
        ], $config ?: include __DIR__ . '/../config/urlsigner.php');

        $this->secretKey = $config['secretKey'];
        $this->defaultExpire = $config['default_expire'];
    }

    /**
     * 链接加密
     * 
     * @param string $url 下载资源接口URL
     * @param string $path 下载文件路径
     * @param null|int $expire 有效时间，秒
     * 
     * @return string 下载链接
     */
    public function sign(string $url, string $path, ?int $expire = null): string
    {
        $params = [];
        $params['_t'] = time();
        $params['_e'] = $expire ?? $this->defaultExpire;
        // 加密 file 参数（可选）
        $params['file'] = Encryptor::encrypt($path, $this->secretKey);
        // 生成签名
        $params['_sign'] = Helpers::generateSignature($params, $this->secretKey);
        // 拼接到URL
        return $url . (str_contains($url, '?') ? '&' : '?') . http_build_query($params);
    }

    /**
     * 验证签名
     * 
     * @param array $params 参数
     * 
     * @return bool
     */
    public function validate(array $params): bool
    {
        if (empty($params['_sign']) || empty($params['_t']) || empty($params['_e'])) {
            return false;
        }
        $sign = $params['_sign'];
        unset($params['_sign']);
        if ((time() - (int)$params['_t']) > (int)$params['_e']) {
            return false; // 链接过期
        }
        return hash_equals($sign, Helpers::generateSignature($params, $this->secretKey));
    }

    /**
     * 解密参数
     * 
     * @param string $file 加密的下载文件路径
     * 
     * @return string 
     */
    public function decryptFileParam(string $file): string
    {
        return Encryptor::decrypt($file, $this->secretKey);
    }
}
