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
 * Wraps the object itself and a list of its fields, methods and metadata.
 */
final class NakedObject
{
    /**
     * POPO to wrap.
     */
    private $_wrapped;

    /**
     * @var NakedClass metadata
     */
    private $_class;

    public function __construct($wrapped, NakedClass $class = null)
    {
        $this->_wrapped = $wrapped;
        $this->_class = $class;
    }

    public function getClass()
    {
        return $this->_class;
    }

    public function __call($name, $args)
    {
        return call_user_func_array(array($this->_wrapped, $name), $args);
    }
}
