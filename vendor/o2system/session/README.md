# O2System Session
[O2System Session](https://github.com/o2system/session) is an Open Source Native PHP Session Management Handler Library.
It allows different cache storage platform to be used.
All but file-based storage require specific server requirements, and a Fatal Exception will be thrown if server requirements are not met.

[O2System Session](https://github.com/o2system/session) is build for working more powerful with [O2System PHP Framework](https://github.com/o2system/o2system), but also can be integrated with other frameworks as standalone PHP Classes Library with limited features.

### Supported Storage Engines Handlers
| Engine | 5.6+ | 7.0+  | &nbsp; |
| ------------- |:-------------:|:-----:| ----- |
| APC | ```Yes``` | ```No``` | http://php.net/apc |
| APCu | ```Yes``` | ```Yes``` | http://php.net/apcu |
| File | ```Yes``` | ```Yes``` | http://php.net/file |
| Memcache | ```Yes``` | ```Yes``` | http://php.net/memcache |
| Memcached | ```Yes``` | ```Yes``` | http://php.net/memcached |
| Redis | ```Yes``` | ```Yes``` | http://redis.io |
| Wincache | ```Yes``` | ```Yes``` | http://php.net/wincache |
| XCache | ```Yes``` | ```No``` | https://xcache.lighttpd.net/ |
| Zend OPCache | ```Yes``` | ```Yes``` | http://php.net/opcache |

### Composer Installation
The best way to install O2System Session is to use [Composer](https://getcomposer.org)
```
composer require o2system/session
```
> Packagist: [https://packagist.org/packages/o2system/session](https://packagist.org/packages/o2system/session)

### Usage
Documentation is available on this repository [wiki](https://github.com/o2system/session/wiki) or visit this repository [github page](https://o2system.github.io/session).

### Ideas and Suggestions
Please kindly mail us at [o2system.framework@gmail.com](mailto:o2system.framework@gmail.com])

### Bugs and Issues
Please kindly submit your [issues at Github](http://github.com/o2system/session/issues) so we can track all the issues along development and send a [pull request](http://github.com/o2system/session/pulls) to this repository.

### System Requirements
- PHP 7.2+
- [Composer](https://getcomposer.org)
- [O2System Kernel](https://github.com/o2system/kernel)

### Credits
|Role|Name|
|----|----|
|Founder and Lead Projects|[Steeven Andrian Salim](http://steevenz.com)|
|Documentation|[Steeven Andrian Salim](http://steevenz.com)
|Github Pages Designer| [Teguh Rianto](http://teguhrianto.tk)

### Supported By
* [Zend Technologies Ltd.](http://zend.com)
