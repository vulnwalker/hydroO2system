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

namespace O2System\Html\Dom;

// ------------------------------------------------------------------------

/**
 * Class Script
 *
 * @package O2System\HTML\DOM
 */
class Script extends \ArrayIterator
{
    /**
     * Script::import
     *
     * @param \O2System\Html\Dom\Script $script
     */
    public function import(Script $script)
    {
        foreach ($script->getArrayCopy() as $scriptTextContent) {
            $this->append($scriptTextContent);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Script::offsetSet
     *
     * @param string $offset
     * @param string $value
     */
    public function offsetSet($offset, $value)
    {
        $value = trim($value);

        if ( ! empty($value)) {
            parent::offsetSet($offset, $value);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Script::__toString
     *
     * @return string
     */
    public function __toString()
    {
        return PHP_EOL . implode(PHP_EOL, $this->getArrayCopy()) . PHP_EOL;
    }
}