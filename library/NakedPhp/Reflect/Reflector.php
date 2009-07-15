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
 * @package    NakedPhp_Reflect
 */

namespace NakedPhp\Reflect;

class Reflector
{
    public function listFields($className)
    {
        $reflector = new \ReflectionClass($className);
        $fields = array();
        foreach ($reflector->getMethods() as $method) {
            if (preg_match('/get[A-Za-z0-9]+/', $method->getName())) {
                $name = lcfirst(str_replace('get', '', $method->getName()));
                $fields[] = $name;
            }
        }
        return $fields;
    }
}
