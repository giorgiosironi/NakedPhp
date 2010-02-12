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

namespace NakedPhp\ProgModel\Facet\Action;
use NakedPhp\MetaModel\NakedObject;
use NakedPhp\MetaModel\Facet\Action\Invocation;

class InvocationMethod implements Invocation
{
    /**
     * @var string method name
     * @example 'doSomething'
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
     * @return mixed    method return value
     */
    public function invoke(NakedObject $no, array $arguments = array())
    {
        $callBack = array($no, $this->_methodName);
        return call_user_func_array($callBack, $arguments);
    }

    /**
     * {@inheritdoc}
     */
    public function facetType()
    {
        return 'Action\Invocation';
    }

    public function __toString()
    {
        return $this->_methodName;
    }
}
