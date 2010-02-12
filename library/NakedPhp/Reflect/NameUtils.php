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

/**
 * Static class containing utilities functions for parsing method names.
 */
class NameUtils
{
    /**
     * Removes the prefix from a method name.
     * <code>
     * NameUtils::baseName('getField');  //returns 'field'
     * </code>
     * @return string   name of the impacted member
     */
    public static function baseName($name)
    {
        $macthes = array();
        preg_match('/[a-z]*([A-Za-z0-9]*)/', $name, $matches);
        return lcfirst($matches[1]);
    }

    /**
     * @param string $methodName
     * @param string $prefix
     * @return boolean              true if $methodName starts with the given $prefix
     */
    public static function startsWith($methodName, $prefix)
    {
        if (strstr($methodName, $prefix) == $methodName) {
            return true;
        }
        return false;
    }
}
