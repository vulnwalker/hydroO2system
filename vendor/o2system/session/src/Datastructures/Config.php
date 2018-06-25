<?php
/**
 * This file is part of the O2System PHP Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         Steeve Andrian Salim
 * @copyright      Copyright (c) Steeve Andrian Salim
 */

// ------------------------------------------------------------------------

namespace O2System\Session\Datastructures;

// ------------------------------------------------------------------------

use O2System\Kernel\Datastructures;

/**
 * Class Config
 *
 * @package O2System\Session\Metadata
 */
class Config extends Datastructures\Config
{
    /**
     * Config::__construct
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        // Define Session Name
        $config[ 'name' ] = isset($config[ 'name' ]) ? $config[ 'name' ] : 'o2session';

        // Define Session Match IP
        $config[ 'match' ][ 'ip' ] = isset($config[ 'match' ][ 'ip' ]) ? $config[ 'match' ][ 'ip' ] : false;

        // Re-Define Session Name base on Match IP
        $config[ 'name' ] = $config[ 'name' ] . ':' . ($config[ 'match' ][ 'ip' ] ? $_SERVER[ 'REMOTE_ADDR' ] . ':' : '');
        $config[ 'name' ] = rtrim($config[ 'name' ], ':');

        if (isset($config[ 'handler' ])) {
            $config[ 'handler' ] = $config[ 'handler' ] === 'files' ? 'file' : $config[ 'handler' ];
            // $config[ 'handler' ] = $config[ 'handler' ] === 'memcache' ? 'memcached' : $config[ 'handler' ];
        }

        if ($config[ 'handler' ] === 'file') {
            if (isset($config[ 'path' ])) {
                $config[ 'path' ] = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $config[ 'path' ]);

                if ( ! is_dir($config[ 'path' ])) {
                    if (defined('PATH_CACHE')) {
                        $config[ 'path' ] = PATH_CACHE . $config[ 'path' ];
                    } else {
                        $config[ 'path' ] = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $config[ 'path' ];
                    }
                }
            } elseif (defined('PATH_CACHE')) {
                $config[ 'path' ] = PATH_CACHE . 'sessions';
            } else {
                $this->path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . implode(
                        DIRECTORY_SEPARATOR,
                        ['o2system', 'cache', 'sessions']
                    );
            }

            $config[ 'path' ] = rtrim($config[ 'path' ], DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

            if ( ! is_writable($config[ 'path' ])) {
                if ( ! file_exists($config[ 'path' ])) {
                    @mkdir($config[ 'path' ], 0777, true);
                }
            }
        }

        if (empty($config[ 'cookie' ]) AND php_sapi_name() !== 'cli') {
            $config[ 'cookie' ] = [
                'name'     => 'o2session',
                'lifetime' => 7200,
                'domain'   => isset($_SERVER[ 'HTTP_HOST' ]) ? $_SERVER[ 'HTTP_HOST' ] : $_SERVER[ 'SERVER_NAME' ],
                'path'     => '/',
                'secure'   => false,
                'httpOnly' => false,
            ];
        }

        if ( ! isset($config[ 'regenerate' ])) {
            $config[ 'regenerate' ][ 'destroy' ] = false;
            $config[ 'regenerate' ][ 'lifetime' ] = 600;
        }

        if ( ! isset($config[ 'lifetime' ])) {
            $config[ 'lifetime' ] = $config[ 'cookie' ][ 'lifetime' ];
        }

        parent::__construct($config, Config::CAMELCASE_OFFSET);
    }
}