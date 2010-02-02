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

namespace NakedPhp\Service\Provider;
use NakedPhp\Service\ServiceDiscoverer;
use NakedPhp\Reflect\ServiceReflector;

abstract class AbstractProvider implements \NakedPhp\Service\ServiceProvider
{
    protected $_discoverer;
    protected $_reflector;

    public function __construct(ServiceDiscoverer $discoverer = null,
                                ServiceReflector $serviceReflector = null)
    {
        $this->_discoverer = $discoverer;
        $this->_reflector = $serviceReflector;
    }

    /**
     * {@inheritdoc}
     */
    public function getServiceClasses()
    {
        $array = array();
        foreach ($this->_discoverer->getList() as $className) {
            $array[$className] = $this->_reflector->analyze($className);
        }
        return $array;
    }

    /**
     * @param object $instance          a service object
     * @param string $fullClassName     the full class name of the object
     * @return NakedBareObject
     */
    protected function _wrap($instance, $fullClassName)
    {
        $nakedClass = $this->_reflector->analyze($fullClassName);
        return new \NakedPhp\ProgModel\NakedBareObject($instance, $nakedClass);
    }
}
