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
 * @package    NakedPhp_Reflect
 */

namespace NakedPhp\Reflect;
use NakedPhp\Metadata\NakedEntityClass;
use NakedPhp\Metadata\NakedMethod;
use NakedPhp\Metadata\NakedParam;
use NakedPhp\Metadata\NakedField;
use NakedPhp\Metadata\Facet\Disabled;
use NakedPhp\Metadata\Facet\Property\Choices;
use NakedPhp\Metadata\Facet\Property\Validate;

class EntityReflector
{
    private $_methodsReflector;
    
    public function __construct(MethodsReflector $methodsReflector = null)
    {
        $this->_methodsReflector = $methodsReflector;
    }
        
    /**
     * TODO: refactor in generic FacetFactory implementations
     * @param string $className
     * @return NakedEntityClass
     */
    public function analyze($className)
    {
        $methods = $this->_methodsReflector->analyze($className);
        $fields = array();
        foreach ($methods as $method) {
            $methodName = $method->getName();
            if ($this->_isGetter($methodName)) {
                $name = str_replace('get', '', $method->getName());
                $fieldName = lcfirst($name);
                $fields[$fieldName] = new NakedField($method->getReturn(), $fieldName);
                continue;
            }
            if ($this->_isSetter($methodName)) {
                continue;
            }
        }

        list($userMethods, $hiddenMethods) = $this->_separateMethods($methods, $fields);

        return new NakedEntityClass($className, $userMethods, $fields, $hiddenMethods);
    }

    /**
     * @param string $methodName
     * @return boolean
     */
    protected function _isGetter($methodName) 
    {
        return preg_match('/get[A-Za-z0-9]+/', $methodName);
    }

    /**
     * @param string $methodName
     * @return boolean
     */
    protected function _isSetter($methodName) 
    {
        return preg_match('/set[A-Za-z0-9]+/', $methodName);
    }

    /**
     * Defines user visible methods, according to which are not used for
     * metadata on $fields.
     * @param array $methods    NakedMethod instances indexed by name
     * @param array $fields     NakedField instances indexed by name
     * @return array            first element is array of user methods,
     *                          second is array of hidden methods.
     */
    protected function _separateMethods(array $methods, array $fields)
    {
        $userMethods = $hiddenMethods = array();
        foreach ($methods as $methodName => $method) {
            foreach ($fields as $fieldName => $field) {
                $pattern = '/[a-z]{1,}' . ucfirst($fieldName) . '/';
                if (preg_match($pattern, $methodName)) {
                    $hiddenMethods[$methodName] = $method;
                    if (strstr($methodName, 'choices')) {
                        $facet = new Choices($fieldName);
                        $field->addFacet($facet);
                    }
                    if (strstr($methodName, 'disable')) {
                        $facet = new Disabled($fieldName);
                        $field->addFacet($facet);
                    }
                    if (strstr($methodName, 'validate')) {
                        $facet = new Validate($fieldName);
                        $field->addFacet($facet);
                    }
                }
            }
            if (!isset($hiddenMethods[$methodName])) {
                $userMethods[$methodName] = $method;
            }
        }
        return array($userMethods, $hiddenMethods);
    }
}
