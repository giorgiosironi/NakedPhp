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
use NakedPhp\ProgModel\PhpSpecification;

class PhpIntrospector
{
    protected $_specification;
    protected $_facetProcessor;
    protected $_reflectionClass;
    protected $_methodRemover;

    public function __construct(PhpSpecification $specification,
                                FacetProcessor $facetProcessor)
    {
        $this->_specification = $specification;
        $this->_facetProcessor = $facetProcessor;
    }

    public function introspectClass()
    {
        $this->_reflectionClass = new \ReflectionClass($this->_specification->getClassName());
        $this->_methodRemover = new ArrayObjectMethodRemover(new \ArrayObject());
        $this->_facetProcessor->processClass($this->_reflectionClass,
                                             $this->_methodRemover,
                                             $this->_specification);
    }
}
