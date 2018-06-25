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

namespace O2System\Html\Dom\Lists;

// ------------------------------------------------------------------------

use O2System\Html\Dom\Element;
use RecursiveIterator;

/**
 * Class Node
 *
 * @package O2System\HTML\DOM\Lists
 */
class Nodes extends \ArrayIterator implements \RecursiveIterator
{
    public $length = 0;

    // ------------------------------------------------------------------------

    public function __construct(\DOMNodeList $nodeList)
    {
        $nodes = [];

        foreach ($nodeList as $node) {
            $this->length++;
            $nodes[] = $node;
        }

        parent::__construct($nodes);
    }

    // ------------------------------------------------------------------------

    public function item($offset)
    {
        return $this->offsetGet($offset);
    }

    // ------------------------------------------------------------------------

    /**
     * Returns if an iterator can be created for the current entry.
     *
     * @link  http://php.net/manual/en/recursiveiterator.haschildren.php
     * @return bool true if the current entry can be iterated over, otherwise returns false.
     * @since 5.1.0
     */
    public function hasChildren()
    {
        return $this->current()->hasChildNodes();
    }

    // ------------------------------------------------------------------------

    /**
     * Returns an iterator for the current entry.
     *
     * @link  http://php.net/manual/en/recursiveiterator.getchildren.php
     * @return RecursiveIterator An iterator for the current entry.
     * @since 5.1.0
     */
    public function getChildren()
    {
        return new self($this->current()->childNodes);
    }

    // ------------------------------------------------------------------------

    public function replace($source)
    {
        foreach ($this as $node) {
            if ($node instanceof Element) {
                $node->replace($source);
            }
        }
    }

    // ------------------------------------------------------------------------

    public function remove()
    {
        foreach ($this as $node) {
            if ($node instanceof Element) {
                if ( ! empty($node->parentNode)) {
                    $node->parentNode->removeChild($node);
                }
            }
        }
    }

    // ------------------------------------------------------------------------

    public function prepend($source)
    {
        foreach ($this as $node) {
            if ($node instanceof Element) {
                $node->append($source);
            }
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    public function append($source)
    {
        foreach ($this as $node) {
            if ($node instanceof Element) {
                $node->prepend($source);
            }
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    public function before($source)
    {
        foreach ($this as $node) {
            if ($node instanceof Element) {
                $node->before($source);
            }
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    public function after($source)
    {
        foreach ($this as $node) {
            if ($node instanceof Element) {
                $node->after($source);
            }
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    public function __empty()
    {
        foreach ($this as $node) {
            if ($node instanceof Element) {
                $node->empty();
            }
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    public function __clone()
    {
        return clone $this;
    }
}