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
use NakedPhp\MetaModel\NakedObjectSpecification;
use NakedPhp\MetaModel\Facet\Action\Invocation;

class InvocationMethod implements Invocation
{
    /**
     * @var string method name
     * @example 'doSomething'
     */
    private $_methodName;

    /**
     * @var NakedObjectSpecification
     */
    private $_returnType;
    
    /**
     * @param string
     */
    public function __construct($methodName = null, NakedObjectSpecification $returnType = null)
    {
        $this->_methodName = $methodName;
        $this->_returnType = $returnType;
    }

    /**
     * @return mixed    method return value
     */
    public function invoke(NakedObject $no, array $arguments = array())
    {
        $callBack = array($no, $this->_methodName);
        $parameters = array();
        foreach ($arguments as $key => $wrapped) {
            if (!($wrapped instanceof NakedObject)) {
                var_dump($wrapped);
                exit;
            }
            $parameters[$key] = $wrapped->getObject();
        }
        $result = call_user_func_array($callBack, $parameters);
        return $no->createNewInstance($result, $this->_returnType);
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
