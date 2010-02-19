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
use NakedPhp\Reflect\ServiceDiscoverer;

abstract class AbstractProvider implements \NakedPhp\Service\ServiceProvider
{
    protected $_discoverer;

    public function __construct(ServiceDiscoverer $discoverer = null)
    {
        $this->_discoverer = $discoverer;
    }

    /**
     * @param object $instance          a service object
     * @param string $fullClassName     the full class name of the object
     * @return NakedBareObject
     */
    protected function _wrap($instance, $fullClassName)
    {
        $specs = $this->_discoverer->getServiceSpecifications();
        $spec = $specs[$fullClassName];
        return new \NakedPhp\ProgModel\NakedBareObject($instance, $spec);
    }

    /**
     * {@inheritdoc}
     */
    public function getServiceSpecifications()
    {
        return $this->_discoverer->getServiceSpecifications();
    }
}
