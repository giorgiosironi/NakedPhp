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
use NakedPhp\Metadata\NakedServiceClass;
use NakedPhp\Metadata\NakedMethod;
use NakedPhp\Metadata\NakedParam;

class ServiceReflector extends AbstractReflector
{
    private $_parser;

    public function __construct(DocblockParser $parser = null)
    {
        $this->_parser = $parser;
    }

    /**
     * @param string $className
     * @return NakedServiceClass
     */
    public function analyze($className)
    {
        $reflector = new \ReflectionClass($className);
        $methods = array();

        foreach ($reflector->getMethods() as $method) {
            $methodName = $method->getName();
            if ($this->_isMagic($methodName)) {
                continue;
            }
            $annotations = $this->_parser->parse($method->getDocComment());
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

        return new NakedServiceClass($className, $methods);
    }

    public function isService($className)
    {
        $reflector = new \ReflectionClass($className);
        $docComment = $reflector->getDocComment();
        if ($this->_parser->contains('NakedService', $docComment)) {
            return true;
        }
        return false;
    }
}
