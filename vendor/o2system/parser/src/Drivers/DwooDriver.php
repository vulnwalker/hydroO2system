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

/**
 * Class DwooDriver
 *
 * This class driver for Dwoo Template Engine for O2System PHP Framework templating system.
 *
 * @package O2System\Parser\Drivers
 */
class DwooDriver extends BaseDriver
{
    /**
     * DwooDriver::initialize
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
                $this->engine = new \Dwoo();
            } else {
                throw new BadThirdPartyException(
                    'PARSER_E_THIRD_PARTY',
                    0,
                    ['BBCode Parser by Jackson Owens', 'https://github.com/jbowens/jBBCode']
                );
            }
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * DwooDriver::isSupported
     *
     * Checks if this template engine is supported on this system.
     *
     * @return bool
     */
    public function isSupported()
    {
        if (class_exists('\Dwoo')) {
            return true;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * DwooDriver::parse
     *
     * @param array $vars Variable to be parsed.
     *
     * @return string
     */
    public function parse(array $vars = [])
    {
        return $this->engine->get(new \Dwoo_Template_String($this->string), $vars);
    }

    // ------------------------------------------------------------------------

    /**
     * DwooDriver::isValidEngine
     *
     * Checks if is a valid Object Engine.
     *
     * @param object $engine Engine Object Resource.
     *
     * @return bool
     */
    protected function isValidEngine($engine)
    {
        if ($engine instanceof \Dwoo) {
            return true;
        }

        return false;
    }
}