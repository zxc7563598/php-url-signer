# hejunjie/url-signer

一个用于生成和验证带签名的 URL 的工具类，支持设置有效期与参数加密。可用于接口防篡改、防盗链、下载链接保护等场景。

> 基于 PHP8 开发，默认支持 SHA256 签名，内置 AES-256-CBC 参数加密能力。

---

## ✨ 功能特性

- ✅ URL 签名与验签
- ✅ 签名过期校验
- ✅ 支持 file 参数加密/解密
- ✅ 自动生成密钥配置（安装时）
- ✅ 无需依赖 Redis、数据库等额外服务

---

## 📦 安装方法

```bash
composer require hejunjie/url-signer
```

安装后会自动生成密钥配置文件：`config/urlsigner.php`。

默认配置文件路径为 config/urlsigner.php，结构如下：

```php
<?php

return [
    'secretKey' => '自动生成的密钥字符串',
    'default_expire' => 3600, // 默认有效期（秒）
];
```

## 🚀 快速使用

### 生成签名链接

```php
use Hejunjie\UrlSigner\UrlSigner;

$signer = new UrlSigner();
$signedUrl = $signer->sign('https://yourdomain.com/download', [
    'file' => 'path/to/secret.pdf'
]);

echo $signedUrl;
// https://yourdomain.com/download?file=加密内容&_t=时间戳&_e=有效期&_sign=签名
```

### 验证签名请求

```php
use Hejunjie\UrlSigner\UrlSigner;

$signer = new UrlSigner();

if (!$signer->validate($_GET)) {
    http_response_code(403);
    exit('非法请求或链接已过期');
}

$file = $signer->decryptParam($_GET['file']);
// 继续处理文件下载逻辑
```

## 🧠 使用示例 - 生成下载链接

以 webman 为例

1. 创建一个下载文件的方法
    ```php
    <?php

    namespace app\controller;

    use support\Request;
    use support\Response;
    use Hejunjie\UrlSigner\UrlSigner;

    class DownloadController
    {

        /**
         * 下载文件通用方法
         * 
         * @param string $path 需要下载的文件路径
         * @method GET
         * 
         * @return Response
         */
        public function download(Request $request): Response
        {
            // 获取所有请求参数
            $param = $request->all();
            // 验证签名有效性
            $urlSigner = new UrlSigner();
            // 如果自定义 secretKey 则可以在构建类时传入配置信息
            // $sign = new UrlSigner([
            //     'secretKey' => 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx', // 32字符长度字符串
            //     'default_expire' => 3600, // 默认有效期（秒）
            // ]);
            $sign = $urlSigner->validate($param);
            if ($sign) {
                // 签名验证成功，正常处理后续逻辑，如果有加密参数可解密，成功则继续处理，失败返回 false
                $path = $urlSigner->decryptParam($param['path']);
                if ($path) {
                    // 解密 path 参数成功，下载文件
                    return response()->download($path);
                }
            }
            return response();
        }
    }
    ```
2. 生成下载链接，在需要生成下载链接的地方：
    ```php
    $url = '指向第一步 download 方法的链接';
    $path = '需要下载的文件路径';

    $urlSigner = new UrlSigner();
    // 生成 60 秒后到期的下载链接
    $download_url = $urlSigner->sign($url, [
        'path' => $urlSigner->encrypt($path)
        // 如果不加密，则不需要调用 encrypt 方法，对应获取参数的方法中也不需要调用 decryptParam 方法
    ], 60);

    echo $download_url;
    ```

## 🔧 更多工具包（可独立使用，也可统一安装）

本项目最初是从 [hejunjie/tools](https://github.com/zxc7563598/php-tools) 拆分而来，如果你想一次性安装所有功能组件，也可以使用统一包：

```bash
composer require hejunjie/tools
```

当然你也可以按需选择安装以下功能模块：

[hejunjie/cache](https://github.com/zxc7563598/php-cache) - 多层缓存系统，基于装饰器模式。

[hejunjie/china-division](https://github.com/zxc7563598/php-china-division) - 中国省市区划分数据包。

[hejunjie/error-log](https://github.com/zxc7563598/php-error-log) - 责任链日志上报系统。

[hejunjie/utils](https://github.com/zxc7563598/php-utils) - 常用工具方法集合。

[hejunjie/address-parser](https://github.com/zxc7563598/php-address-parser) - 收货地址智能解析工具，支持从非结构化文本中提取用户/地址信息。

[hejunjie/mobile-locator](https://github.com/zxc7563598/php-mobile-locator) - 国内手机号归属地 & 运营商识别。

[hejunjie/google-authenticator](https://github.com/zxc7563598/php-google-authenticator) - Google Authenticator 及类似应用的密钥生成、二维码创建和 OTP 验证。

[hejunjie/simple-rule-engine](https://github.com/zxc7563598/php-simple-rule-engine) - 一个轻量、易用的 PHP 规则引擎，支持多条件组合、动态规则执行。

👀 所有包都遵循「轻量实用、解放双手」的原则，能单独用，也能组合用，自由度高，欢迎 star 🌟 或提 issue。

---

该库后续将持续更新，添加更多实用功能。欢迎大家提供建议和反馈，我会根据大家的意见实现新的功能，共同提升开发效率。
