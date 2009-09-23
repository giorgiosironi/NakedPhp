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
 * @category   Example
 * @package    Example_Model
 */

/**
 * @NakedService
 */
class Example_Model_CityFactory
{
    /**
     * @param string $name  the city name
     */
    public function createCity($name)
    {
        return new Example_Model_City($name);
    }

    public function __toString()
    {
        return 'CityFactory';
    }
}

