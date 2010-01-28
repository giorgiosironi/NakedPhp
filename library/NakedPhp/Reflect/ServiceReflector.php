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
use NakedPhp\Metadata\NakedServiceSpecification;
use NakedPhp\Metadata\NakedObjectAction;
use NakedPhp\Metadata\NakedParam;
use NakedPhp\Metadata\Facet\Action\Invocation;

class ServiceReflector
{
    private $_parser;
    private $_methodsReflector;

    public function __construct(DocblockParser $parser = null, MethodsReflector $methodsReflector = null)
    {
        $this->_parser = $parser;
        $this->_methodsReflector = $methodsReflector;
    }

    /**
     * @param string $className
     * @return NakedServiceSpecification
     */
    public function analyze($className)
    {
        $methods = $this->_methodsReflector->analyze($className);

        $class = new NakedServiceSpecification($className, $methods);
        foreach ($methods as $methodName => $method) {
            $method->addFacet(new Invocation($methodName));
        }

        return $class;
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
