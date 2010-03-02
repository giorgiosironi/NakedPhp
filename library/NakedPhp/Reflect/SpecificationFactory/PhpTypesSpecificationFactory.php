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

namespace NakedPhp\Reflect\SpecificationFactory;
use NakedPhp\ProgModel\PhpSpecification;
use NakedPhp\Reflect\SpecificationFactory;

class PhpTypesSpecificationFactory implements SpecificationFactory
{
    protected $_basicTypes;

    public function __construct()
    {
        $this->_basicTypes = array('bool', 'integer', 'float', 'string', 'array', 'void');
    }

    /**
     * @return array    PhpSpecification instances
     */
    public function getSpecifications()
    {
        $specifications = array();
        foreach ($this->_basicTypes as $type) {
            $specifications[$type] = new PhpSpecification($type, null, null);
        }
        return $specifications;
    }
}
