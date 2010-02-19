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
use NakedPhp\Reflect\ServiceDiscoverer;
use NakedPhp\Reflect\SpecificationLoader;

class ConfiguredServiceDiscoverer implements ServiceDiscoverer
{
    protected $_specLoader;
    protected $_serviceClassNames;
    protected $_specs;

    public function __construct(SpecificationLoader $specLoader = null,
                                array $serviceClassNames = null)
    {
        $this->_specLoader        = $specLoader;
        $this->_serviceClassNames = $serviceClassNames;
    }

    /**
     * {@inheritdoc}
     */
    public function getServiceSpecifications()
    {
        if (!isset($this->_specs)) {
            $this->_specs = array();
            foreach ($this->_serviceClassNames as $className) {
                $spec = $this->_specLoader->loadSpecification($className);
                $spec->markAsService();
                $this->_specs[$className] = $spec;
            }
        }
        return $this->_specs;
    }
}
