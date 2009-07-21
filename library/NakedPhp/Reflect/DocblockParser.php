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

class DocblockParser
{
    public function foo() {}
    /**
     * @param string $docblock
     * @return array info on the @param and @return annotations contained
     */
    public function parse($docblock)
    {
        $lines = explode("\n", $docblock);
        $result = array();
        foreach ($lines as $line) {
            if (strstr($line, '* @')) {
                $line = ltrim($line, ' *');
                if (strstr($line, '@param')) {
                    $result[] = $this->_extractParam($line);
                } else if (strstr($line, '@return')) {
                    $result[] = $this->_extractReturn($line);
                }
            }
        }
        return $result;
    }
    
    protected function _extractParam($line)
    {
        list ($annotation, $type, $name, $description) = preg_split('/[ ]/', $line, 4);
        return array(
            'annotation'  => str_replace('@', '', $annotation),
            'type'        => $type,
            'name'        => str_replace('$', '', $name),
            'description' => trim($description)
        );
    }

    protected function _extractReturn($line)
    {
        $pieces = preg_split('/[ ]/', $line, 3);
        if (count($pieces) == 3) {
            list ($annotation, $type, $description) = $pieces; 
        } else {
            list ($annotation, $type) = $pieces; 
            $description = '';
        }
        return array(
            'annotation'  => str_replace('@', '', $annotation),
            'type'        => $type,
            'description' => trim($description)
        );
    }
}
