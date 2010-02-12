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
 */

namespace NakedPhp;

/**
 * This loader addresses autoload of namespace classes in the include path
 * until Zend_Loader will take care of this.
 */
class Loader
{
    public function autoload($class)
    {
        if (strstr($class, '\\')) {
            $classFile = str_replace('\\', '/', $class) . '.php';
            include_once $classFile;
            if (!(class_exists($class) or interface_exists($class))) {
                throw new \Exception("Namespaced class $class not found (included file: $classFile.");
            }
            return true;
        }
        return false;
    }
}
