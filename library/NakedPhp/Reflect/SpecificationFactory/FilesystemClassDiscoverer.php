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

namespace NakedPhp\Reflect\SpecificationFactory;

/**
 * This class discover php classes in a folder of the filesystem.
 */
class FilesystemClassDiscoverer implements ClassDiscoverer
{
    private $_folder;
    private $_prefix;

    public function __construct($folder, $prefix = '')
    {
        $this->_folder = $folder;
        $this->_prefix = $prefix;
    }
    
    public function getList()
    {
        $classes = array();
        foreach (new \DirectoryIterator($this->_folder) as $file) {
            $filename = $file->getFilename();
            if ($this->_getExtension($filename) == '.php') {
                $className = $this->_prefix . $this->_getBaseClassName($filename);
                $classes[] = $className;
            }
        }
        return $classes;
    }

    private function _getExtension($filename)
    {
        $point = strrpos($filename, '.');
        return substr($filename, $point);
    }

    private function _getBaseClassName($filename)
    {
        $point = strrpos($filename, '.');
        return substr($filename, 0, $point);
    }
}
