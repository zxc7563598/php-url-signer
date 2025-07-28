# hejunjie/url-signer

<div align="center">
  <a href="./README.md">English</a>｜<a href="./README.zh-CN.md">简体中文</a>
  <hr width="50%"/>
</div>

一个用于生成和验证带签名的 URL 的工具类，支持设置有效期与参数加密。可用于接口防篡改、防盗链、下载链接保护等场景。

> 基于 PHP8 开发，默认支持 SHA256 签名，内置 AES-256-CBC 参数加密能力。

**本项目已经经由 Zread 解析完成，如果需要快速了解项目，可以点击此处进行查看：[了解本项目](https://zread.ai/zxc7563598/php-url-signer)**

---

## ✨ 功能特性

- ✅ URL 签名与验签
- ✅ 签名过期校验
- ✅ 支持 file 参数加密/解密
- ✅ 自动生成密钥配置（安装时）
- ✅ 无需依赖 Redis、数据库等额外服务

---

## 📦 安装方法

使用 Composer 安装：

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

[hejunjie/utils](https://github.com/zxc7563598/php-utils) - 一个零碎但实用的 PHP 工具函数集合库。包含文件、字符串、数组、网络请求等常用函数的工具类集合，提升开发效率，适用于日常 PHP 项目辅助功能。

[hejunjie/cache](https://github.com/zxc7563598/php-cache) - 基于装饰器模式实现的多层缓存系统，支持内存、文件、本地与远程缓存组合，提升缓存命中率，简化缓存管理逻辑。

[hejunjie/china-division](https://github.com/zxc7563598/php-china-division) - 定期更新，全国最新省市区划分数据，身份证号码解析地址，支持 Composer 安装与版本控制，适用于表单选项、数据校验、地址解析等场景。

[hejunjie/error-log](https://github.com/zxc7563598/php-error-log) - 基于责任链模式的错误日志处理组件，支持多通道日志处理（如本地文件、远程 API、控制台输出），适用于复杂日志策略场景。

[hejunjie/mobile-locator](https://github.com/zxc7563598/php-mobile-locator) - 基于国内号段规则的手机号码归属地查询库，支持运营商识别与地区定位，适用于注册验证、用户画像、数据归档等场景。

[hejunjie/address-parser](https://github.com/zxc7563598/php-address-parser) - 收货地址智能解析工具，支持从非结构化文本中提取姓名、手机号、身份证号、省市区、详细地址等字段，适用于电商、物流、CRM 等系统。

[hejunjie/url-signer](https://github.com/zxc7563598/php-url-signer) - 用于生成带签名和加密保护的URL链接的PHP工具包，适用于需要保护资源访问的场景

[hejunjie/google-authenticator](https://github.com/zxc7563598/php-google-authenticator) - 一个用于生成和验证时间基础一次性密码（TOTP）的 PHP 包，支持 Google Authenticator 及类似应用。功能包括密钥生成、二维码创建和 OTP 验证。

[hejunjie/simple-rule-engine](https://github.com/zxc7563598/php-simple-rule-engine) - 一个轻量、易用的 PHP 规则引擎，支持多条件组合、动态规则执行，适合业务规则判断、数据校验等场景。

👀 所有包都遵循「轻量实用、解放双手」的原则，能单独用，也能组合用，自由度高，欢迎 star 🌟 或提 issue。

---

该库后续将持续更新，添加更多实用功能。欢迎大家提供建议和反馈，我会根据大家的意见实现新的功能，共同提升开发效率。
