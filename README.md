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

$file = $signer->decryptFileParam($_GET['file']);
// 继续处理文件下载逻辑
```

## 🧠 原理说明

所有参数按字典序排序，使用 HMAC-SHA256 和 secretKey 生成签名。

默认附带 _t（时间戳）和 _e（有效期）两个参数。

验证签名时会自动判断是否超时失效。

file 参数可加密后传输，避免资源路径泄露。

## 🧭 用途 & 初衷
有时候，我们只想简单地保护一个链接，不想上 Redis、不想查数据库、不想绕一大圈。

比如：

- 做个下载接口，希望别人别乱改参数瞎访问；
- 想加个链接时效，几分钟内有效，过期就失效；
- 不想把文件路径明文暴露出来，哪怕只是个 PDF；

所以我就写了这个小工具，签名 + 加密，够用、轻巧、易接入，

不依赖任何扩展，不依赖 Redis，纯 PHP 就能跑，适合做一些接口层的小保护。

不是什么黑科技，但胜在省心实用。

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

👀 所有包都遵循「轻量实用、解放双手」的原则，能单独用，也能组合用，自由度高，欢迎 star 🌟 或提 issue。

---

该库后续将持续更新，添加更多实用功能。欢迎大家提供建议和反馈，我会根据大家的意见实现新的功能，共同提升开发效率。
