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

class Example_Model_Place
{
    private $_name = 'Default Name';
    private $_city;
    private $_category;
    private $_website;
    private $_address;
    private $_phone;

    /**
     * @return string   the name
     */
    public function getName()
    {
        return $this->_name;
    }

    public function setName($name)
    {
        $this->_name = $name;
    }

    /**
     * @return Example_Model_City
     */
    public function getCity()
    {
        return $this->_city;
    }

    public function setCity(Example_Model_City $city)
    {
        $this->_city = $city;
    }
    
    /**
     * @return Example_Model_PlaceCategory
     */
    public function getCategory()
    {
        return $this->_category;
    }

    public function setCategory($category)
    {
        $this->_category = $category;
    }


    /**
     * @return string
     * */
    public function getWebsite()
    {
        return $this->_website;
    }

    public function setWebsite($website)
    {
        $this->_website = $website;
    }

    /**
     * @return string
     * */
    public function getAddress()
    {
        return $this->_address;
    }

    public function setAddress($address)
    {
        $this->_address = $address;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->_phone;
    }

    public function setPhone($phone)
    {
        $this->_phone = $phone;
    }

    /**
     * @param string $name              the name of pub, disco
     * @return Example_Model_Place      this object
     */
    public function edit($name)
    {
        $this->_name = (string) $name;
        return $this;
    }

    public function __toString()
    {
        return (string) $this->_name;
    }
}

