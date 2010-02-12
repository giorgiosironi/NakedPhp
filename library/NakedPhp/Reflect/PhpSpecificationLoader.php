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
    protected $_factory;

    /**
     * @var array   PhpSpecification instances
     */
    protected $_specifications;

    public function __construct(SpecificationFactory $factory)
    {
        $this->_factory = $factory;
    }

    public function init()
    {
        $this->_specifications = $this->_factory->getSpecifications();
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
}
