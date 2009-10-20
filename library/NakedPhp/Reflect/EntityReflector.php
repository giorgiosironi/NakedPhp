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

class EntityReflector extends AbstractReflector
{
    private $_parser;

    public function __construct(DocblockParser $parser = null)
    {
        $this->_parser = $parser;
    }

    /**
     * @param string $className
     * @return NakedEntityClass
     */
    public function analyze($className)
    {
        $reflector = new \ReflectionClass($className);
        $fields = array();
        $methods = array();
        foreach ($reflector->getMethods() as $method) {
            $methodName = $method->getName();
            $annotations = $this->_parser->parse($method->getDocComment());
            if ($this->_isGetter($methodName)) {
                if ($this->_isHidden($method->getDocComment())) {
                    continue;
                }
                $name = str_replace('get', '', $method->getName());
                $fieldName = lcfirst($name);
                foreach ($annotations as $a) {
                    if ($a['annotation'] == 'return') {
                        $fields[$fieldName] = new NakedField($a['type'], $fieldName);
                        // $a['description']
                    }
                }
                if (!isset($fields[$fieldName])) {
                    $fields[$fieldName] = new NakedField('string', $fieldName);
                }
                continue;
            }
            if ($this->_isSetter($methodName)) {
                continue;
            }

            if ($this->_isMagic($methodName)) {
                continue;
            }

            $params = array();
            $return = 'void';
            foreach ($annotations as $ann) {
                if ($ann['annotation'] == 'param') {
                    $params[$ann['name']] = new NakedParam($ann['type'], $ann['name']);
                } else if ($ann['annotation'] == 'return') {
                    $return = $ann['type'];
                }
            }
            $methods[$methodName] = new NakedMethod($methodName, $params, $return);
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
     * @param string $docblock  documentation block of a method or property
     * @return boolean
     */
    protected function _isHidden($docblock)
    {
        return $this->_parser->contains('Hidden', $docblock);
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
                }
            }
            if (!isset($hiddenMethods[$methodName])) {
                $userMethods[$methodName] = $method;
            }
        }
        return array($userMethods, $hiddenMethods);
    }
}
