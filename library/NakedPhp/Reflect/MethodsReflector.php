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
use NakedPhp\ProgModel\PhpAction;
use NakedPhp\ProgModel\PhpActionParameter;
use NakedPhp\ProgModel\OneToOneAssociation;

class MethodsReflector
{
    private $_parser;

    public function __construct(DocblockParser $parser = null)
    {
        $this->_parser = $parser;
    }

    /**
     * @return string
     */
    public function getIdentifierForAction(\ReflectionMethod $method)
    {
        return $method->getName();
    }
 
    /**
     * @return string
     */
    public function getIdentifierForAssociation(\ReflectionMethod $getter)
    {
        $methodName = $getter->getName();
        return NameUtils::baseName($methodName);
    }

    /**
     * @return array    key 'specificationName' is class name or data type,
     *                      or null if cannot be found
     */
    public function getReturnType(\ReflectionMethod $method)
    {
        $phpdocAnnotations = $this->_parser->getPhpdocAnnotations($method->getDocComment());
        $returnType = null;
        foreach ($phpdocAnnotations as $ann) {
            if ($ann['annotation'] == 'return') {
                $returnType = $ann['type'];
            }
        }
        $returnType = array(
            'specificationName' => $returnType
        );

        $nakedPhpAnnotations = $this->_parser->getNakedPhpAnnotations($method->getDocComment());
        foreach ($nakedPhpAnnotations as $ann => $value) {
            if ($ann == 'TypeOf') {
                $returnType['typeOf'] = $value[0];
            }
        }
        return $returnType;
    }

    /**
     * @return array    of array, indexed by param identifier
     *                  subarrays' keys are 'type' and 'description'
     */
    public function getParameters(\ReflectionMethod $method)
    {
        $annotations = $this->_parser->getPhpdocAnnotations($method->getDocComment());
        $params = array();
        foreach ($annotations as $ann) {
            if ($ann['annotation'] == 'param') {
                $params[$ann['name']] = array(
                    'specificationName' => $ann['type'],
                    'description' => $ann['description']
                );
            }
        }
        return $params;
    }
 
    // FIX: from now on, old Api. Delete.

    /**
     * @param string $className
     * @return array    PhpAction instances
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
            $annotations = $this->_parser->getPhpdocAnnotations($method->getDocComment());

            if ($this->_isMagic($methodName)) {
                continue;
            }

            $params = array();
            $return = 'void';
            $parametersAnnotationsFound = $returnAnnotationFound = false;
            foreach ($annotations as $ann) {
                if ($ann['annotation'] == 'param') {
                    $params[$ann['name']] = new PhpActionParameter($ann['type'], $ann['name']);
                    $parametersAnnotationsFound = true;
                } else if ($ann['annotation'] == 'return') {
                    $return = $ann['type'];
                    $returnAnnotationFound = true;
                }
            }
            if (!$parametersAnnotationsFound) {
                foreach ($method->getParameters() as $param) {
                    $name = $param->getName();
                    $params[$name] = new PhpActionParameter('string', $name);
                }
            }
            if (!$returnAnnotationFound) {
                $return = 'string';
            }
            $methods[$methodName] = new PhpAction($methodName, $params, $return);
        }

        return $methods; 
    }

    /**
     * Maybe hidden methods should be listed anyway, but then the
     * Invocation facet would have to be created here
     * @param string $docblock  documentation block of a method or property
     * @return bool
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
