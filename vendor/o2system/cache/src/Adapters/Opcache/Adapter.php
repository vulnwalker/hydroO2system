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

namespace O2System\Cache\Adapters\Opcache;

// ------------------------------------------------------------------------

use O2System\Cache\Abstracts\AbstractAdapter;

/**
 * Class Adapter
 *
 * @package O2System\Cache\Adapters\Apc
 */
abstract class Adapter extends AbstractAdapter
{
    /**
     * Adapter::$platform
     *
     * Adapter Platform Name
     *
     * @var string
     */
    protected $platform = 'Zend OPCache';

    // ------------------------------------------------------------------------

    /**
     * Adapter::connect
     *
     * @param array $config Cache adapter connection configuration.
     *
     * @return void
     */
    public function connect(array $config)
    {
        $this->config = $config;

        // Zend OPCache adapter do not require further processing.
    }

    // ------------------------------------------------------------------------

    /**
     * Adapter::increment
     *
     * Increment a raw value offset.
     *
     * @param string $key  Cache item key.
     * @param int    $step Increment step to add.
     *
     * @return mixed New value on success or FALSE on failure.
     */
    public function increment($key, $step = 1)
    {
        return apc_inc($this->prefixKey . $key, $step);
    }

    // ------------------------------------------------------------------------

    /**
     * Adapter::decrement
     *
     * Decrement a raw value offset.
     *
     * @param string $key  Cache item key.
     * @param int    $step Decrement step to add.
     *
     * @return mixed New value on success or FALSE on failure.
     */
    public function decrement($key, $step = 1)
    {
        return apc_dec($this->prefixKey . $key, $step);
    }

    // ------------------------------------------------------------------------

    /**
     * Adapter::getInfo
     *
     * Gets item pool adapter info.
     *
     * @return mixed
     */
    public function getInfo()
    {
        return opcache_get_configuration();
    }

    // ------------------------------------------------------------------------

    /**
     * Adapter::getInfo
     *
     * Gets item pool adapter stats.
     *
     * @return mixed
     */
    public function getStats()
    {
        return call_user_func_array('opcache_get_status', func_get_args());
    }

    // ------------------------------------------------------------------------

    /**
     * Adapter::isSupported
     *
     * Checks if this adapter is supported on this system.
     *
     * @return bool Returns FALSE if not supported.
     */
    public function isSupported()
    {
        return (bool)(extension_loaded('Zend OPCache') && ini_get('opcache.enable'));
    }

    // ------------------------------------------------------------------------

    /**
     * Adapter::isConnected
     *
     * Checks if this adapter has a successful connection.
     *
     * @return bool Returns FALSE if not supported.
     */
    public function isConnected()
    {
        return (bool)(function_exists('opcache_get_status'));
    }
}