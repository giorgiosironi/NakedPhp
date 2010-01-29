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
use NakedPhp\Metadata\NakedEntitySpecification;
use NakedPhp\Metadata\NakedObjectAction;
use NakedPhp\Metadata\NakedObjectActionParameter;
use NakedPhp\Metadata\OneToOneAssociation;

class MethodsReflector
{
    private $_parser;

    public function __construct(DocblockParser $parser = null)
    {
        $this->_parser = $parser;
    }
 
    /**
     * @param string $className
     * @return NakedEntitySpecification
     */
    public function analyze($className)
    {
        $reflector = new \ReflectionClass($className);
        $methods = array();
        foreach ($reflector->getMethods() as $method) {
            /** @var ReflectionMethod $method */
            if ($this->_isHidden($method->getDocComment())) {
                continue;
            }

            $methodName = $method->getName();
            $annotations = $this->_parser->parse($method->getDocComment());

            if ($this->_isMagic($methodName)) {
                continue;
            }

            $params = array();
            $return = 'void';
            $parametersAnnotationsFound = $returnAnnotationFound = false;
            foreach ($annotations as $ann) {
                if ($ann['annotation'] == 'param') {
                    $params[$ann['name']] = new NakedObjectActionParameter($ann['type'], $ann['name']);
                    $parametersAnnotationsFound = true;
                } else if ($ann['annotation'] == 'return') {
                    $return = $ann['type'];
                    $returnAnnotationFound = true;
                }
            }
            if (!$parametersAnnotationsFound) {
                foreach ($method->getParameters() as $param) {
                    $name = $param->getName();
                    $params[$name] = new NakedObjectActionParameter('string', $name);
                }
            }
            if (!$returnAnnotationFound) {
                $return = 'string';
            }
            $methods[$methodName] = new NakedObjectAction($methodName, $params, $return);
        }

        return $methods; 
    }

    /**
     * TODO: maybe hidden methods should be listed anyway, but then the
     * Invocation facet would have to be created here
     * @param string $docblock  documentation block of a method or property
     * @return boolean
     */
    protected function _isHidden($docblock)
    {
        return $this->_parser->contains('Hidden', $docblock);
    }

    protected function _isMagic($methodName)
    {
        return substr($methodName, 0, 2) == '__';
    }
}
