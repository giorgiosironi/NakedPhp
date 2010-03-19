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
 * @Service
 */
class Example_Model_PlaceFactory extends NakedPhp\Storage\AbstractFactoryAndRepository
{
    /**
     * @return Example_Model_Place
     */
    public function createPlace()
    {
        return new Example_Model_Place();
    }

    /**
     * @param Example_Model_City $city
     * @return Example_Model_Place
     */
    public function createPlaceFromCity(Example_Model_City $city)
    {
        $place = new Example_Model_Place();
        $place->setCity($city);
        return $place;
    }

    /**
     * @return array
     * @TypeOf(Example_Model_Place)
     */
    public function createSomePlaces()
    {
        $firstPlace = new Example_Model_Place();
        $firstPlace->setName('Amnesia');
        $secondPlace = new Example_Model_Place();
        $secondPlace->setName('MacLaren\'s Pub');
        return array($firstPlace, $secondPlace);
    }

    /**
     * @param string $name  name of the category (disco, pub...)
     * @return Example_Model_PlaceCategory
     */
    public function createPlaceCategory($name)
    {
        return new Example_Model_PlaceCategory($name);
    }

    /**
     * @param string $name  the city name
     * @return Example_Model_City
     */
    public function createCity($name)
    {
        return new Example_Model_City($name);
    }

    /**
     * @return array
     * @TypeOf(Example_Model_City)
     */
    public function createSomeCities()
    {
        return array(
            new Example_Model_City('New York'),
            new Example_Model_City('Moscow'),
            new Example_Model_City('Madrid'),
            new Example_Model_City('London')
        );
    }

    /**
     * @return array
     * @TypeOf(Example_Model_City)
     */
    public function findAllCities()
    {
        return $this->_em->getRepository('Example_Model_City')->findAll();
    }

    public function hideFindAllCities()
    {
        return !((bool) count($this->_em->getRepository('Example_Model_City')->findAll()));
    }

    public function __toString()
    {
        return 'PlaceFactory';
    }
}

