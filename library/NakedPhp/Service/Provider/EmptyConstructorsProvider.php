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

class EmptyConstructorsProvider implements \NakedPhp\Service\ServiceProvider
{
    private $_discoverer;
    private $_reflector;

    public function __construct(\NakedPhp\Service\ServiceDiscoverer $discoverer = null, \NakedPhp\Reflect\ServiceReflector $serviceReflector = null)
    {
        $this->_discoverer = $discoverer;
        $this->_reflector = $serviceReflector;
    }

    /**
     * @return array    NakedServiceClass instances
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
     * @param string $className
     * @return NakedService
     */
    public function getService($className)
    {
        $fullClassName = /*'\\' .*/ $className;
        $wrapped = new $fullClassName();
        $nakedClass = $this->_reflector->analyze($className);
        return new \NakedPhp\Metadata\NakedBareService($wrapped, $nakedClass);
    }
}
