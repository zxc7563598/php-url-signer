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
        ], $config ?: Installer::generateKey());

        $this->secretKey = $config['secretKey'];
        $this->defaultExpire = $config['default_expire'];
    }

    /**
     * 生成签名
     * 
     * @param string $url 下载资源接口URL
     * @param array $params 需要传递的参数
     * @param null|int $expire 有效时间，秒
     * 
     * @return string 下载链接
     */
    public function sign(string $url, array $params, ?int $expire = null): string
    {
        $params['_t'] = time();
        $params['_e'] = $expire ?? $this->defaultExpire;
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
     * 加密参数
     * 
     * @param string $value 
     * 
     * @return string 
     */
    public function encrypt(string $value): string
    {
        return Encryptor::encrypt($value, $this->secretKey);
    }

    /**
     * 解密参数
     * 
     * @param string $value 需要解密的数据
     * 
     * @return string 
     */
    public function decryptParam(string $value): string
    {
        return Encryptor::decrypt($value, $this->secretKey);
    }
}
