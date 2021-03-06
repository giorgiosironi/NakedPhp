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
    /**
     * Search for '@' . $annotation in $classDocblock
     * @return bool
     */
    public function contains($annotationName, $classDocblock)
    {
        $annotations = $this->getNakedPhpAnnotations($classDocblock);
        return isset($annotations[$annotationName]);
    }

    public function getNakedPhpAnnotations($docblock)
    {
        $lines = explode("\n", $docblock);
        $result = array();
        foreach ($lines as $line) {
            $matches = array();
            if (preg_match('/@([A-Z]{1}[A-Za-z0-9]*)(.*)/', $line, $matches)) {
                $annotationName = $matches[1];
                $parameters = $this->_extractAnnotationParams($matches[2]);
                if ($parameters) {
                    $result[$annotationName] = $parameters;
                } else {
                    $result[$annotationName] = true;
                }
            }
        }
        return $result;
    }

    /**
     * @param string $parametersString  such as '(paramOne=20, paramTwo=4)'
     * @return array                    pairs of names and values of parameters
     */
    protected function _extractAnnotationParams($parametersString)
    {
        $result = array();
        $parametersMatches = array();
        if (preg_match('/\((.*)\)/', $parametersString, $parametersMatches)) {
            $parameters = preg_split('/[ ,]/', $parametersMatches[1]);
            foreach ($parameters as $param) {
                $valuesMatches = array();
                if (preg_match('/([A-Za-z0-9_]*)=([A-Za-z0-9_]*)/', $param, $valuesMatches)) {
                    $result[$valuesMatches[1]] = $valuesMatches[2];
                } else if (preg_match('/([A-Za-z0-9_]*)/', $param, $valuesMatches)) {
                    $result[] = $valuesMatches[1];
                }
            }
        }
        return $result;
    }

    /**
     * @param string $functionDocblock
     * @return array info on the @param and @return annotations contained
     */
    public function getPhpdocAnnotations($functionDocblock)
    {
        $lines = explode("\n", $functionDocblock);
        $result = array();
        foreach ($lines as $line) {
            if (strstr($line, '* @')) {
                $line = ltrim($line, ' *');
                if (strstr($line, '@param')) {
                    $result[] = $this->_extractDocblockParam($line);
                } else if (strstr($line, '@return')) {
                    $result[] = $this->_extractDocblockReturn($line);
                }
            }
        }
        return $result;
    }
    
    protected function _extractDocblockParam($line)
    {
        $pieces = preg_split('/[ ]/', $line, 4);
        $annotation  = $pieces[0];
        $type        = $pieces[1];
        $name        = isset($pieces[2]) ? $pieces[2] : uniqid();
        $description = isset($pieces[3]) ? $pieces[3] : '';
        return array(
            'annotation'  => str_replace('@', '', $annotation),
            'type'        => $type,
            'name'        => str_replace('$', '', $name),
            'description' => trim($description)
        );
    }

    protected function _extractDocblockReturn($line)
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
