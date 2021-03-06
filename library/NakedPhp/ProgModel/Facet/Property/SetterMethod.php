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
 * POSTPONED: tranform in new Setter(\ReflectionMethod)
 * if possible.
 */
class SetterMethod implements Setter
{
    /**
     * @var string method name
     * @example 'setMyField'
     */
    private $_methodName;
    
    /**
     * @param string
     */
    public function __construct($methodName)
    {
        $this->_methodName = $methodName;
    }

    /**
     * Sets $value on $no by calling $this->_methodName.
     */
    public function setProperty(NakedObject $no, NakedObject $value)
    {
        $methodName = $this->_methodName;
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
