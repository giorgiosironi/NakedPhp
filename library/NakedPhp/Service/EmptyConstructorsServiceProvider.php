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

class EmptyConstructorsServiceProvider implements ServiceProvider
{
    private $_discoverer;

    public function __construct(ServiceDiscoverer $discoverer = null, $serviceReflector = null)
    {
        $this->_discoverer = $discoverer;
    }

    /**
     * @return array    NakedServiceClass instances
     */
    public function getServiceClasses()
    {
        return array_combine($this->_discoverer->getList(), $this->_discoverer->getList());
    }

    /**
     * @param string $className
     * @return NakedService
     */
    public function getService($className)
    {
        $fullClassName = '\\' . $className;
        $wrapped = new $fullClassName();
        return new \NakedPhp\Metadata\NakedService($wrapped, null);
    }
}
