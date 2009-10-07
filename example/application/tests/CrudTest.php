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
 * @package    Example_Test
 */

require_once 'AbstractTest.php';

class Example_CrudTest extends Example_AbstractTest
{
    public function testFactoryIsLoaded()
    {
        $this->dispatch('/naked-php/view/type/service/object/Example_Model_PlaceFactory');
        $this->assertQueryContentContains('#methods a', 'createCity');
        $this->assertQueryContentContains('#methods a', 'createPlaceCategory');
        $this->assertQueryContentContains('#methods a', 'createPlace');
    }

    /**
     * @depends testFactoryIsLoaded
     */
    public function testCityFactoryMethodRequiresName()
    {
        $this->dispatch('/naked-php/call/type/service/object/Example_Model_PlaceFactory/method/createCity');
        $this->assertQuery('form input#name');
    }

    /**
     * @depends testCityFactoryMethodRequiresName
     */
    public function testCityFactoryMethodCreatesCityInstance()
    {
        $this->getRequest()
             ->setMethod('POST')
             ->setPost(array(
                 'name' => 'New York'
             ));
        $this->dispatch('/naked-php/call/type/service/object/Example_Model_PlaceFactory/method/createCity');
        $this->assertRedirectTo('/naked-php/view/type/entity/object/1');

        $this->resetRequest()
             ->resetResponse();
        $this->dispatch('/naked-php/view/type/entity/object/1');
        $this->assertQueryContentContains('#nakedphp_session', 'New York');
        $this->assertQueryContentContains('.nakedphp_entity.Example_Model_City .name',
                                          'New York');
    }

    /**
     * @depends testCityFactoryMethodCreatesCityInstance
     */
    public function testCategoryFactoryMethodRequiresName()
    {
        $this->dispatch('/naked-php/call/type/service/object/Example_Model_PlaceFactory/method/createPlaceCategory');
        $this->assertQuery('form input#name');
    }

    /**
     * @depends testCategoryFactoryMethodRequiresName
     */
    public function testCategoryFactoryMethodCreatesCategoryInstance()
    {
        $this->getRequest()
             ->setMethod('POST')
             ->setPost(array(
                 'name' => 'Disco'
             ));
        $this->dispatch('/naked-php/call/type/service/object/Example_Model_PlaceFactory/method/createPlaceCategory');
        $this->assertRedirectTo('/naked-php/view/type/entity/object/1');

        $this->resetRequest()
             ->resetResponse();
        $this->dispatch('/naked-php/view/type/entity/object/1');
        $this->assertQueryContentContains('#nakedphp_session', 'Disco');
        $this->assertQueryContentContains('.nakedphp_entity.Example_Model_PlaceCategory .name',
                                          'Disco');
    }
}
