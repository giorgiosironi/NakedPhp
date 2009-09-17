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

class EntityReflector
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
            $annotations = $this->_parser->parse($method->getDocComment());
            if (preg_match('/get[A-Za-z0-9]+/', $method->getName())) {
                $name = str_replace('get', '', $method->getName());
                $fieldName = lcfirst($name);
                $fields[$fieldName] = new \stdClass;
                continue;
            }
            if (preg_match('/set[A-Za-z0-9]+/', $method->getName())) {
                continue;
            }

            $methodName = $method->getName();

            if (substr($methodName, 0, 2) == '__') {
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
}
