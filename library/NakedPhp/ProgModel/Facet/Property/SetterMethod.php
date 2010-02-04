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
 * @package    NakedPhp_ProgModel
 */

namespace NakedPhp\ProgModel\Facet\Property;
use NakedPhp\MetaModel\Facet;
use NakedPhp\MetaModel\Facet\Property\Setter;
use NakedPhp\MetaModel\NakedObject;

/**
 * TODO: tranform in new Setter(\ReflectionMethod)
 */
class SetterMethod implements Setter
{
    /**
     * @var string property name
     * @example 'myField'
     */
    private $_propertyName;
    
    /**
     * @param string
     */
    public function __construct($propertyName)
    {
        $this->_propertyName = $propertyName;
    }

    /**
     * Sets a value on $no the $this->_propertyName field.
     */
    public function setProperty(NakedObject $no, NakedObject $value)
    {
        $methodName = 'set' . ucfirst($this->_propertyName);
        $unwrapped = $value->getObject();
        return $no->$methodName($unwrapped);
    }

    /**
     * {@inheritdoc}
     */
    public function facetType()
    {
        return 'Property\Setter';
    }
}
