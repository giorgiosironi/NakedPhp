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
 * @package    NakedPhp_Service
 */

namespace NakedPhp\Service;
use NakedPhp\ProgModel\NakedBareObject;
use NakedPhp\Reflect\SpecificationLoader;

class NakedFactory implements \NakedPhp\MetaModel\NakedFactory
{
    protected $_specificationLoader;

    public function __construct(SpecificationLoader $specificationLoader = null)
    {
        $this->_specificationLoader  = $specificationLoader;
    }

    /**
     * @param mixed $value
     * @return NakedObject
     * FIX: it should wrap also scalar values
     */
    public function createBare($object)
    {
        if (is_object($object)) {
            $className = get_class($object);
        } else {
            $className = gettype($object);
        }
        $spec = $this->_specificationLoader->loadSpecification($className);
        return new NakedBareObject($object, $spec);
    }
}
