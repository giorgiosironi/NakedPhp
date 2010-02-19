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

class PhpSpecificationLoader implements SpecificationLoader
{
    protected $_specFactory;
    protected $_introspectorFactory;

    /**
     * @var array   PhpSpecification instances
     */
    protected $_specifications;

    public function __construct(SpecificationFactory $specFactory,
                                IntrospectorFactory $introspectorFactory)
    {
        $this->_specFactory         = $specFactory;
        $this->_introspectorFactory = $introspectorFactory;
    }

    public function init()
    {
        $this->_specifications = $this->_specFactory->getSpecifications();
        foreach ($this->_specifications as $spec) {
            $introspector = $this->_introspectorFactory->getIntrospector($spec);
            $introspector->introspectClass();
            $introspector->introspectAssociations();
            $introspector->introspectActions();
        }
    }

    /**
     * @return PhpSpecification
     */
    public function loadSpecification($className)
    {
        foreach ($this->_specifications as $name => $spec) {
            if ($name == $className) {
                return $spec;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getServiceSpecifications()
    {
        $serviceSpecs = array();
        foreach ($this->_specifications as $name => $spec) {
            if ($spec->isService()) {
                $serviceSpecs[$name] = $spec;
            }
        }
        return $serviceSpecs;
    }
}
