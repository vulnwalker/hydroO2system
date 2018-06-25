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
use O2System\Parser\Engines\Noodle;

/**
 * Class NoodleDriver
 *
 * @package O2System\Parser\Drivers
 */
class NoodleDriver extends AbstractDriver
{
    /**
     * NoodleDriver::initialize
     *
     * @param array $config
     *
     * @return static
     */
    public function initialize(array $config)
    {
        if (empty($this->engine)) {
            $this->engine = new Noodle($config);
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * NoodleDriver::parse
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
     * NoodleDriver::isSupported
     *
     * Checks if this template engine is supported on this system.
     *
     * @return bool
     */
    public function isSupported()
    {
        if (class_exists('\O2System\Parser\Engines\Noodle')) {
            return true;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * NoodleDriver::isValidEngine
     *
     * Checks if is a valid Object Engine.
     *
     * @param object $engine Engine Object Resource.
     *
     * @return bool
     */
    protected function isValidEngine($engine)
    {
        if ($engine instanceof Noodle) {
            return true;
        }

        return false;
    }
}