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

use O2System\Html\Element;
use O2System\Spl\Iterators\ArrayIterator;

/**
 * Class Nodes
 *
 * @package O2System\Html\Element
 */
class Nodes extends ArrayIterator
{
    private $nodesEntities = [];

    public function createNode($tagName, $entityName = null)
    {
        if ($tagName instanceof Element) {
            $this->push($tagName);
        } else {
            $this->push(new Element($tagName, $entityName));
        }

        return $this->last();
    }

    public function push($value)
    {
        parent::push($value);
        $this->nodesEntities[] = $this->last()->entity->getEntityName();
    }

    public function hasNode($index)
    {
        if (is_string($index) and in_array($index, $this->nodesEntities)) {
            if (false !== ($key = array_search($index, $this->nodesEntities))) {
                if ($this->offsetExists($key)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function getNode($index)
    {
        if (is_string($index) and in_array($index, $this->nodesEntities)) {
            if (false !== ($key = array_search($index, $this->nodesEntities))) {
                if ($this->offsetExists($key)) {
                    return $this->offsetGet($index);
                }
            }
        }

        return false;
    }

    public function item($index)
    {
        return $this->offsetGet($index);
    }

    public function prepend($value)
    {
        parent::unshift($value);
    }

    public function getNodeByTagName($tagName)
    {
        $result = [];

        foreach ($this as $node) {
            if ($node->tagName === $tagName) {
                $result[] = $node;
            }
        }

        return $result;
    }

    public function getNodeByEntityName($entityName)
    {
        if (false !== ($index = array_search($entityName, $this->nodesEntities))) {
            if ($this->offsetExists($index)) {
                return $this->offsetGet($index);
            }
        }

        return false;
    }
}