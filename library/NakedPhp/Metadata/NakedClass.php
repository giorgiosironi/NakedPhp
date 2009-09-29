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
 * Wraps properties about a domain class.
 * @abstract    not declared abstract to allow testing of base functionality
 */
class NakedClass
{
    protected $_className;

    /**
     * @var array available methods
     */
    protected $_methods;

    /**
     * @param array $methods    NakedMethod instances; keys are method names
     */
    public function __construct($className = '', array $methods = array())
    {
        $this->_className = $className;
        $this->_methods = $methods;
    }

    /**
     * @return array $methods    NakedMethod instances; keys are method names
     */
    public function getMethods()
    {
        return $this->_methods;
    }

    /**
     * @param string $name
     * @return NakedMethod
     */
    public function getMethod($name)
    {
        return $this->_methods[$name];
    }

    /**
     * @return string   the fully qualified class name
     */
    public function getClassName()
    {
        return $this->_className;
    }
}
