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
 * @package    NakedPhp_Stubs
 */

namespace NakedPhp\Stubs;

class User
{
    private $_name;
    private $_active = true;

    public function getName()
    {
        return $this->_name;
    }

    public function setName($name)
    {
        $this->_name = $name;
    }

    public function getStatus()
    {
        return 'guest';
    }

    /**
     * @param string $title     subject of message
     * @param string $text      html text
     */
    public function sendMessage($title, $text)
    {
        // don't do anything
    }

    /**
     * @return boolean
     */
    public function disactivate()
    {
        $this->_active = false;
    }

    public function activate()
    {
        $this->_active = true;
    }
}
