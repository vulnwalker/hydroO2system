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
 * Class SmartyDriver
 *
 * This class driver for Smarty Template Engine for O2System PHP Framework templating system.
 *
 * @package O2System\Parser\Drivers
 */
class SmartyDriver extends BaseDriver
{
    /**
     * SmartyDriver::initialize
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
                $this->engine = new \Smarty();
            } else {
                throw new BadThirdPartyException(
                    'PARSER_E_THIRD_PARTY',
                    0,
                    ['Smarty Template Engine by New Digital Group, Inc', 'http://www.smarty.net/']
                );
            }
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * SmartyDriver::isSupported
     *
     * Checks if this template engine is supported on this system.
     *
     * @return bool
     */
    public function isSupported()
    {
        if (class_exists('\Smarty')) {
            return true;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * SmartyDriver::parse
     *
     * @param array $vars Variable to be parsed.
     *
     * @return string
     */
    public function parse(array $vars = [])
    {
        foreach ($vars as $_assign_key => $_assign_value) {
            $this->engine->assign($_assign_key, $_assign_value);
        }

        return $this->engine->fetch('string:' . $this->string);
    }

    // ------------------------------------------------------------------------

    /**
     * SmartyDriver::isValidEngine
     *
     * Checks if is a valid Object Engine.
     *
     * @param object $engine Engine Object Resource.
     *
     * @return bool
     */
    protected function isValidEngine($engine)
    {
        if ($engine instanceof \Smarty) {
            return true;
        }

        return false;
    }
}