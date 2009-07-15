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
class NakedObject
{
    /**
     * POPO to wrap.
     */
    private $_wrapped;

    /**
     * @var array names (with first letter uppercase) of properties
     */
    private $_fields;

    /**
     * @var array available methods
     */
    private $_methods;

    public function __construct($wrapped, array $fields = array(), array $methods = array())
    {
        $this->_wrapped = $wrapped;
        $this->_fields = $fields;
        $this->_methods = $methods;
    }

    public function getFields()
    {
        return $this->_fields;
    }

    public function getMethods()
    {
        return $this->_methods;
    }

    public function __call($name, $args)
    {
        return call_user_func_array(array($this->_wrapped, $name), $args);
    }
}
