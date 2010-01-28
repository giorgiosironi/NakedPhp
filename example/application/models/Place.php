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

/**
 * @Entity
 */
class Example_Model_Place
{
    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $_id;

    /** @Column(type="string") */
    private $_name = 'Default Name';

    /**
     * @OneToOne(targetEntity="Example_Model_City")
     * @JoinColumn(name="city_id", referencedColumnName="_id")
     */
    private $_city;

    /**
     * @OneToOne(targetEntity="Example_Model_PlaceCategory")
     * @JoinColumn(name="category_id", referencedColumnName="_id")
     */
    private $_category;

    /** @Column(type="string") */
    private $_website;
    /** @Column(type="string") */
    private $_address;
    /** @Column(type="string") */
    private $_phone;

    /**
     * @OneToMany(targetEntity="Example_Model_Place", mappedBy="_place")
     */
    private $_events;

    public function __construct()
    {
        $this->_events = new Doctrine\Common\Collections\ArrayCollection();
    }

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

    public function addEvent(Example_Model_Event $event)
    {
        if ($this->_events->contains($event)) {
            return;
        }
        $this->_events->add($event);
        $event->setPlace($this);
    }

    /**
     * @return Doctrine\Common\Collections\Collection
     */
    public function getEvents()
    {
        return $this->_events;
    }

    /**
     * @param string $name              the name of pub, disco
     * @return Example_Model_Place      this object
     */
    public function giveMeAName($name)
    {
        $this->setName($name);
        return $this;
    }

    public function __toString()
    {
        return (string) $this->_name;
    }
}

