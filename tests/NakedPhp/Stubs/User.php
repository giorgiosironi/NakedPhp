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

/**
 * @Entity
 */
class User
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $_id;

    /**
     * @Column(type="string")
     */
    private $_name;

    /**
     * @Column(type="boolean")
     */
    private $_active = true;

    /**
     * @Column(type="string", nullable=true)
     */
    private $_phonenumber;
        
    public function __construct($name = null)
    {
        $this->_name = $name;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    public function setName($name)
    {
        $this->_name = $name;
    }

    public function getPassword()
    {
        return 'fake';
    }

    /**
     * @return NakedPhp\Stubs\Phonenumber
     */
    public function getPhonenumber()
    {
        return $this->_phonenumber;
    }

    public function setPhonenumber(Phonenumber $phonenumber)
    {
        $this->_phonenumber = $phonenumber;
    }

    public function validatePhonenumber($phonenumber)
    {
        return true;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return 'guest';
    }

    public function choicesStatus()
    {
        return array('guest', 'user', 'admin');
    }

    public function disableStatus()
    {
        return false;
    }

    /**
     * @param string $title     subject of message
     * @param string $text      html text
     * @return void
     */
    public function sendMessage($title, $text)
    {
        // don't do anything
    }

    /**
     * It never hides the method, but at least we can test for
     * the Hidden Facet to be present.
     */
    public function hideSendMessage()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function deactivate()
    {
        $this->_active = false;
    }

    public function activate()
    {
        $this->_active = true;
    }

    /**
     * @Hidden
     */
    public function mySkippedMethod()
    {
    }

    public function __toString()
    {
        return 'User: ' . $this->getName();
    }

    /**
     * Creates a bunch of stdClass instances, just to test
     * array management.
     * @return array
     * @TypeOf(stdClass)
     */
    public function createMyStandardClasses()
    {
        return array(
            new \stdClass,
            new \stdClass,
            new \stdClass
        );
    }
}
