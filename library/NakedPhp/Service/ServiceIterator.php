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

/**
 * This class iterates over a ServiceProvider.
 */
class ServiceIterator implements \IteratorAggregate
{
    private $_serviceProvider;

    public function __construct(ServiceProvider $provider)
    {
        $this->_serviceProvider = $provider;
    }
    
    public function getIterator()
    {
        $array = array();
        foreach ($this->_serviceProvider->getServiceClasses() as $className => $nakedServiceClass) {
            $array[$className] = $this->_serviceProvider->getService($className);
        }
        return new \ArrayIterator($array);
    }
}
