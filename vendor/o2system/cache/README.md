# O2System Cache
O2System Cache is an Open Source Cache Management Adapters Library. This allows the O2System Framework to integrate with some of the most popular cache storage engines. All but file-based caching require specific server requirements, and a Fatal Exception will be thrown if server requirements are not met. O2System Cache is build for working more powerful within O2System Framework, but also can be used for integrated with others as standalone version with limited features.

O2System Cache is written based on PSR-6: Caching Interface and PSR-16: Common Interface for Caching Libraries. 

### Supported Storage Engines Adapters
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
The best way to install O2System Cache is to use [Composer](https://getcomposer.org)
```
composer require o2system/cache
```
> Packagist: [https://packagist.org/packages/o2system/cache](https://packagist.org/packages/o2system/Cache)

### Usage
```php
use O2System\Cache;

$cache = new Cache\Adapters\Opcache\ItemPool();

if( $cache->isConnected() ) {
    // Save cache
    $cache->save( new Cache\Item( 'cacheKeyName', 'This is cache content, support any type of data', 300 ) );
    // Get cache
    echo $cache->getItem( 'cacheKeyName' )->get();
}
```
> Output: This is cache content, support any type of data

Documentation is available on this repository [wiki](https://github.com/o2system/cache/wiki) or visit this repository [github page](https://o2system.github.io/cache).

### Ideas and Suggestions
Please kindly mail us at [o2system.framework@gmail.com](mailto:o2system.framework@gmail.com])

### Bugs and Issues
Please kindly submit your [issues at Github](http://github.com/o2system/cache/issues) so we can track all the issues along development and send a [pull request](http://github.com/o2system/cache/pulls) to this repository.

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
