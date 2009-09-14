<?php
/**
 * Naked Php is a framework that implements the Naked Objects pattern.
 * @copyright Copyright (C) 2009  Giorgio Sironi
 * @license http://www.gnu.org/licenses/lgpl-2.1.txt 
 * 
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * @category   NakedPhp
 * @package    NakedPhp_Metadata
 */

namespace NakedPhp\Metadata;

/**
 * Decorator for a domain object.
 * Wraps the object itself.
 * @abstract    not marked as abstract to allow testing of base behavior
 */
class NakedObject
{
    /**
     * POPO to wrap.
     */
    protected $_wrapped;

    public function __construct($wrapped)
    {
        $this->_wrapped = $wrapped;
    }

    public function getClassName()
    {
        return get_class($this->_wrapped);
    }

    public function __call($name, $args)
    {
        return call_user_func_array(array($this->_wrapped, $name), $args);
    }

    public function __toString()
    {
        if (method_exists($this->_wrapped, '__toString')) {
            $result = (string) $this->_wrapped;
        } else {
            $result = 'OBJECT';
        }
        return $result;
    }
}
