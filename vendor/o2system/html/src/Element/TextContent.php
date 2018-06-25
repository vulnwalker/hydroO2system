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

namespace O2System\Html\Element;

// ------------------------------------------------------------------------

use O2System\Spl\Iterators\ArrayIterator;

/**
 * Class TextContent
 *
 * @package O2System\Html\Element
 */
class TextContent extends ArrayIterator
{
    public function replace($value)
    {
        return $this->exchangeArray([
            $value,
        ]);
    }

    public function item($index)
    {
        return $this->offsetGet($index);
    }

    public function prepend($value)
    {
        parent::unshift($value);
    }
}