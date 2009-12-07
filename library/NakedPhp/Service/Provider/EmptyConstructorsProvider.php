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
use NakedPhp\Service\Provider\AbstractProvider;
use NakedPhp\Service\ServiceDiscoverer;
use NakedPhp\Reflect\ServiceReflector;

class EmptyConstructorsProvider extends AbstractProvider
{
    /**
     * {@inheritdoc}
     */
    public function getService($className)
    {
        $wrapped = new $className();
        return $this->_wrap($wrapped, $className);
    }
}
