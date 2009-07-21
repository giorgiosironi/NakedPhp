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
 * @package    NakedPhp_Metadata
 */

namespace NakedPhp\Metadata;

final class NakedMethod
{
    /**
     * @var string
     */
    private $_name;

    /**
     * @var array   NakedParam instances
     */
    private $_params;

    /**
     * @var string
     */
    private $_returnType;

    public function __construct($name, array $params = array(), $return = 'void')
    {
        $this->_name = $name;
        $this->_params = $params;
        $this->_returnType = $return;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function getParams()
    {
        return $this->_params;
    }

    public function getReturn()
    {
        return $this->_returnType;
    }

    public function __toString()
    {
        return $this->getName();
    }
}
