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
use NakedPhp\Metadata\NakedClass;
use NakedPhp\Metadata\NakedMethod;
use NakedPhp\Metadata\NakedParam;

class Reflector
{
    private $_parser;

    public function __construct(DocblockParser $parser)
    {
        $this->_parser = $parser;
    }

    /**
     * @param string $className
     * @return NakedClass
     */
    public function analyze($className)
    {
        $reflector = new \ReflectionClass($className);
        $fields = array();
        foreach ($reflector->getMethods() as $method) {
            $annotations = $this->_parser->parse($method->getDocComment());
            if (preg_match('/get[A-Za-z0-9]+/', $method->getName())) {
                $name = str_replace('get', '', $method->getName());
                $fields[] = lcfirst($name);
            } else if (!preg_match('/set[A-Za-z0-9]+/', $method->getName())) {
                $params = array();
                $return = 'void';
                foreach ($annotations as $ann) {
                    if ($ann['annotation'] == 'param') {
                        $params[$ann['name']] = new NakedParam($ann['type'], $ann['name']);
                    } else if ($ann['annotation'] == 'return') {
                        $return = $ann['type'];
                    }
                }
                $methods[] = new NakedMethod($method->getName(), $params, $return);
            }
        }

        return new NakedClass($fields, $methods);
    }
}
