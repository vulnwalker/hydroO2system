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
 * Class BBCodeDriver
 *
 * This class driver for Parse BBCode for O2System PHP Framework templating system.
 *
 * @package O2System\Parser\Drivers
 */
class BBCodeDriver extends BaseDriver
{
    /**
     * BBCodeDriver::initialize
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
                $this->engine = new \JBBCode\Parser();
                $this->engine->addCodeDefinitionSet(new \JBBCode\DefaultCodeDefinitionSet());
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
     * BBCodeDriver::isSupported
     *
     * Checks if this template engine is supported on this system.
     *
     * @return bool
     */
    public function isSupported()
    {
        if (class_exists('\JBBCode\Parser')) {
            return true;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * BBCodeDriver::parse
     *
     * @param array $vars Variable to be parsed.
     *
     * @return string
     */
    public function parse(array $vars = [])
    {
        $this->engine->parse($this->string);

        return $this->engine->getAsHtml();
    }

    // ------------------------------------------------------------------------

    /**
     * BBCodeDriver::isValidEngine
     *
     * Checks if is a valid Object Engine.
     *
     * @param object $engine Engine Object Resource.
     *
     * @return bool
     */
    protected function isValidEngine($engine)
    {
        if ($engine instanceof \JBBCode\Parser) {
            return true;
        }

        return false;
    }
}