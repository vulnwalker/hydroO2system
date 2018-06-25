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

namespace O2System\Parser\Drivers;

// ------------------------------------------------------------------------

use O2System\Parser\Abstracts\AbstractDriver;
use O2System\Parser\Engines\Shortcodes;

/**
 * Class ShortcodesDriver
 *
 * @package O2System\Parser\Drivers
 */
class ShortcodesDriver extends AbstractDriver
{
    /**
     * ShortcodesDriver::initialize
     *
     * @param array $config
     *
     * @return $this
     * @throws \O2System\Core\Exceptions\BadThirdPartyException
     */
    public function initialize(array $config)
    {
        if (empty($this->engine)) {
            $this->engine = new Shortcodes($config);
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * ShortcodesDriver::parse
     *
     * @param array $vars Variable to be parsed.
     *
     * @return string
     */
    public function parse(array $vars = [])
    {
        return $this->engine->parseString($this->string, $vars);
    }

    // ------------------------------------------------------------------------

    /**
     * ShortcodesDriver::isSupported
     *
     * Checks if this template engine is supported on this system.
     *
     * @return bool
     */
    public function isSupported()
    {
        if (class_exists('\O2System\Parser\Engines\Shortcodes')) {
            return true;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * ShortcodesDriver::isValidEngine
     *
     * Checks if is a valid Object Engine.
     *
     * @param object $engine Engine Object Resource.
     *
     * @return bool
     */
    protected function isValidEngine($engine)
    {
        if ($engine instanceof Shortcodes) {
            return true;
        }

        return false;
    }
}