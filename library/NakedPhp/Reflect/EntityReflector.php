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
                $name = str_replace('get', '', $method->getName());
                $fieldName = lcfirst($name);
                foreach ($annotations as $a) {
                    if ($a['annotation'] == 'return') {
                        $fields[$fieldName] = new NakedField($a['type'], $fieldName);
                        // $a['description']
                    }
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

        return new NakedEntityClass($methods, $fields);
    }

    protected function _isGetter($methodName) 
    {
        return preg_match('/get[A-Za-z0-9]+/', $methodName);
    }

    protected function _isSetter($methodName) 
    {
        return preg_match('/set[A-Za-z0-9]+/', $methodName);
    }
}
