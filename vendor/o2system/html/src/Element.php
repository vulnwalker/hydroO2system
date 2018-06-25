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

namespace O2System\Html;

// ------------------------------------------------------------------------

use O2System\Html\Element\Attributes;
use O2System\Html\Element\Entity;
use O2System\Html\Element\Metadata;
use O2System\Html\Element\Nodes;
use O2System\Html\Element\TextContent;
use O2System\Spl\Iterators\ArrayIterator;

/**
 * Class Element
 *
 * @package O2System\Html
 */
class Element
{
    public $tagName;
    public $entity;
    public $attributes;
    public $textContent;
    public $childNodes;
    public $metadata;

    public function __construct($tagName, $entityName = null)
    {
        $this->tagName = trim($tagName);

        $this->entity = new Entity();
        $this->entity->setEntityName($entityName);

        $this->attributes = new Attributes();
        $this->textContent = new TextContent();
        $this->childNodes = new Nodes();
        $this->metadata = new Metadata();
    }

    public function __clone()
    {
        $newElement = $this;
        $reflection = new \ReflectionClass($this);

        foreach ($reflection->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            $value = $property->getValue($newElement);

            if (is_object($value)) {
                if ($value instanceof ArrayIterator) {
                    $value = new ArrayIterator($value->getArrayCopy());
                    $property->setValue($newElement, $value);
                } else {
                    $property->setValue($newElement, clone $value);
                }
            } else {
                $property->setValue($newElement, $value);
            }
        }

        return $newElement;
    }

    public function __toString()
    {
        return $this->render();
    }

    public function render()
    {
        $selfClosingTags = [
            'area',
            'base',
            'br',
            'col',
            'command',
            'embed',
            'hr',
            'img',
            'input',
            'keygen',
            'link',
            'meta',
            'param',
            'source',
            'track',
            'wbr',
        ];

        if (in_array($this->tagName, $selfClosingTags)) {
            $attr = $this->attributes;
            unset($attr[ 'realpath' ]);

            if ($this->hasAttributes()) {
                return '<' . $this->tagName . ' ' . trim($this->attributes->__toString()) . '>';
            }

            return '<' . $this->tagName . '>';
        } else {
            $output[] = $this->open();

            if ($this->hasTextContent()) {
                $output[] = PHP_EOL . implode('', $this->textContent->getArrayCopy()) . PHP_EOL;
            }

            if ($this->hasChildNodes()) {
                if ( ! $this->hasTextContent()) {
                    $output[] = PHP_EOL;
                }

                foreach ($this->childNodes as $childNode) {
                    $output[] = $childNode . PHP_EOL;
                }
            }
        }

        $output[] = $this->close();

        return implode('', $output);
    }

    public function hasAttributes()
    {
        return (bool)($this->attributes->count() == 0 ? false : true);
    }

    /**
     * Tag Open Method
     *
     * @access public
     *
     * @return string
     */
    public function open()
    {
        $attr = $this->attributes;
        unset($attr[ 'realpath' ]);

        if ($this->hasAttributes()) {
            return '<' . $this->tagName . ' ' . trim($this->attributes->__toString()) . '>';
        }

        return '<' . $this->tagName . '>';
    }

    // ------------------------------------------------------------------------

    public function hasTextContent()
    {
        return (bool)($this->textContent->count() == 0 ? false : true);
    }

    public function hasChildNodes()
    {
        return (bool)($this->childNodes->count() == 0 ? false : true);
    }

    /**
     * Tag Close Method
     *
     * @access public
     *
     * @return string
     */
    public function close()
    {
        return '</' . $this->tagName . '>';
    }
}