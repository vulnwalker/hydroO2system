# O2System Filesystem
O2System Filesystem is an Open Source PHP Convenience Library for reading, writing and appending data from and into files and directories, which is built for working more powerfully with O2System Framework, but it also can be used with other frameworks as a standalone version with limited features.

### Supported Files Processor and Generator
- CSV File
- INI File
- JSON File
- XML File
- Zip File

### Features Handlers
- File Handler and Manipulation
- Directory Handler and Manipulation
- Uploaders
- Downloaders with speed limit and resumeable support
- File Transfer Protocol (FTP)

### Composer Installation
The best way to install O2System Filesystem is to use [Composer](https://getcomposer.org)
```
composer require o2system/filesystem
```
> Packagist: [https://packagist.org/packages/o2system/filesystem](https://packagist.org/packages/o2system/filesystem)

### Usage
```php
use O2System\Filesystem\Files;

// Write a CSV file example
$csvFile = new Files\CsvFile();
$csvFile->createFile( 'path/to/files/filename.csv' );
$csvFile->store( 'foo', 'bar' );
$csvFile->writeFile();

// File download handler
$downloader = new Handlers\Downloader( 'path/to/files/downloadthis.zip' );
$downloader
    ->speedLimit( 1024 )
    ->resumeable( true );

// Send the requested download file
$downloader->download();
```

Documentation is available on this repository [wiki](https://github.com/o2system/filesystem/wiki) or visit this repository [github page](https://o2system.github.io/filesystem).

### Ideas and Suggestions
Please kindly mail us at [o2system.framework@gmail.com](mailto:o2system.framework@gmail.com])

### Bugs and Issues
Please kindly submit your [issues at Github](http://github.com/o2system/filesystem/issues) so we can track all the issues along development and send a [pull request](http://github.com/o2system/filesystem/pulls) to this repository.

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
