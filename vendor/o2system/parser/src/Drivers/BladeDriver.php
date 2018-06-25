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

use O2System\Core\Exceptions\BadThirdPartyException;
use O2System\Parser\Abstracts\AbstractDriver;
use O2System\Parser\Engines\Blade;

/**
 * Class BladeDriver
 *
 * This class driver for Laravel's Blade Template Engine for O2System PHP Framework templating system.
 *
 * @package O2System\Parser\Drivers
 */
class BladeDriver extends AbstractDriver
{
    /**
     * BladeDriver::initialize
     *
     * @param array $config
     *
     * @return $this
     * @throws \O2System\Core\Exceptions\BadThirdPartyException
     */
    public function initialize(array $config)
    {
        if (empty($this->engine)) {
            if ($this->isSupported()) {
                $this->engine = new Blade($config);
            } else {
                throw new BadThirdPartyException('PARSER_E_THIRD_PARTY', 0, ['\O2System\Parser\Engines\Blade']);
            }
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * BaseDriver::isSupported
     *
     * Checks if this template engine is supported on this system.
     *
     * @return bool
     */
    public function isSupported()
    {
        if (class_exists('\O2System\Parser\Engines\Blade')) {
            return true;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * BladeDriver::parse
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
     * BladeDriver::isValidEngine
     *
     * Checks if is a valid Object Engine.
     *
     * @param object $engine Engine Object Resource.
     *
     * @return bool
     */
    protected function isValidEngine($engine)
    {
        if ($engine instanceof Blade) {
            return true;
        }

        return false;
    }
}