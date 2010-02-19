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

class FactoryProvider extends AbstractProvider
{
    protected $_factory;

    public function __construct(ServiceDiscoverer $discoverer = null,
                                $factory)
    {
        parent::__construct($discoverer);
        $this->_factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function getService($fullClassName)
    {
        $baseName = $this->_getBaseName($fullClassName);
        $method = 'get' . $baseName;
        $wrapped = $this->_factory->$method();
        return $this->_wrap($wrapped, $fullClassName);
    }

    /**
     * @param string $fullClassName     
     * @return string   the base name from the last \ or _
     */
    private function _getBaseName($fullClassName)
    {
        $lastNamespaceSeparator = $this->_findLast('\\', $fullClassName);
        $lastUnderscoreSeparator = $this->_findLast('_', $fullClassName);
        $lastSeparator = max($lastNamespaceSeparator, $lastUnderscoreSeparator);
        return $this->_sliceFromAfter($lastSeparator, $fullClassName);
    }

    private function _findLast($needle, $string)
    {
        return strrpos($string, $needle);
    }

    private function _sliceFromAfter($position, $string)
    {
        if ($position === false) {
            return $string;
        }
        return substr($string, $position + 1);
    }
}
