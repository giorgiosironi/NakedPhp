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
use NakedPhp\Metadata\NakedBareEntity;
use NakedPhp\Metadata\NakedService;
use NakedPhp\Reflect\EntityReflector;
use NakedPhp\Reflect\ServiceReflector;

class NakedFactory
{
    protected $_entityReflector;
    protected $_serviceReflector;

    public function __construct(EntityReflector $entityReflector = null, ServiceReflector $serviceReflector = null)
    {
        $this->_entityReflector  = $entityReflector;
        $this->_serviceReflector = $serviceReflector;
    }

    public function create($object)
    {
        $className = get_class($object);
        if ($this->_serviceReflector->isService($className)) {
            $class = $this->_serviceReflector->analyze($className);
            return new NakedService($object, $class);
        } else {
            $class = $this->_entityReflector->analyze($className);
            return new NakedBareEntity($object, $class);
        }
    }
}
