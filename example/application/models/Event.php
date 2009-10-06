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

class Example_Model_Event
{
    private $_name;
    private $_description;
    private $_place;
    private $_startTime;
    private $_endTime;

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
     * @return string   the description
     */
    public function getDescription()
    {
        return $this->_description;
    }

    public function setDescription($description)
    {
        $this->_description = $description;
    }

    /**
     * @return Example_Model_Place
     */
    public function getPlace()
    {
        return $this->_place;
    }

    public function setPlace(Example_Model_Place $place)
    {
        $this->_place = $place;
    }

    /**
     * @return string   the start time
     */
    public function getStartTime()
    {
        return $this->_startTime;
    }

    public function setStartTime($startTime)
    {
        $this->_startTime = $startTime;
    }

    /**
     * @return string   the endTime
     */
    public function getEndTime()
    {
        return $this->_endTime;
    }

    public function setEndTime($endTime)
    {
        $this->_endTime = $endTime;
    }

    public function __toString()
    {
        return (string) $this->_name;
    }
}

