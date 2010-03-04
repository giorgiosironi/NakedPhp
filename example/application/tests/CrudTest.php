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
        $this->assertNotQuery('#object .button.edit');
        $this->assertNotQuery('#object .button.remove');
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
                 'name' => 'Sidney'
             ));
        $this->dispatch('/naked-php/call/type/service/object/Example_Model_PlaceFactory/method/createCity');
        $this->assertRedirectTo('/naked-php/view/type/entity/object/1');

        $this->resetRequest()
             ->resetResponse();
        $this->dispatch('/naked-php/view/type/entity/object/1');
        $this->assertQueryContentContains('#nakedphp_session', 'Sidney');
        $this->assertQueryContentContains('.nakedphp_entity.Example_Model_City .name',
                                          'Sidney');
        $this->assertQuery('#object .button.edit');
        $this->assertQuery('#object .button.remove');
    }

    /**
     * @depends testCityFactoryMethodRequiresName
     */
    public function testMultipleCitiesFactoryMethodCreatesAnArray()
    {
        $this->getRequest()
             ->setMethod('POST')
             ->setPost(array());
        $this->dispatch('/naked-php/call/type/service/object/Example_Model_PlaceFactory/method/createSomeCities');
        $this->assertRedirectTo('/naked-php/view/type/entity/object/1');

        $this->resetRequest()
             ->resetResponse();
        $this->dispatch('/naked-php/view/type/entity/object/1');
        echo $this->response->getBody();
        $this->assertQueryContentContains('#nakedphp_session', '4 Example_Model_City');
        $this->assertQueryContentContains('.nakedphp_collection.Example_Model_City tr td',
                                          'New York');
        $this->assertQueryContentContains('.nakedphp_collection.Example_Model_City tr td',
                                          'Madrid');
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

    /**
     * @depends testCityFactoryMethodCreatesCityInstance
     */
    public function testPlaceEditingDisplaysOtherEntitiesAsSelectable()
    {
        $this->dispatch('/naked-php/call/type/service/object/Example_Model_PlaceFactory/method/createPlace');

        $this->resetRequest()
             ->resetResponse();
        $this->getRequest()
             ->setMethod('POST')
             ->setPost(array(
                 'name' => 'Sidney'
             ));
        $this->dispatch('/naked-php/call/type/service/object/Example_Model_PlaceFactory/method/createCity');

        $this->resetRequest()
             ->resetResponse();
        $this->dispatch('/naked-php/edit/type/entity/object/1');
        $this->assertQueryContentContains('.nakedphp_entity.Example_Model_Place select[name="city"] option', 'Sidney');
    }

    /**
     * @depends testCityFactoryMethodCreatesCityInstance
     */
    public function testPlaceEditingConservesContext()
    {
        $this->dispatch('/naked-php/call/type/service/object/Example_Model_PlaceFactory/method/createPlace');

        $this->resetRequest()
             ->resetResponse();
        $this->dispatch('/naked-php/edit/type/entity/object/1');
        $this->assertQuery('#nakedphp_context li a');//[href="**"]');
        $this->assertQuery('.nakedphp_entity.Example_Model_Place select[name="city"]');

        $this->resetRequest()
             ->resetResponse();
        $this->getRequest()
             ->setMethod('POST')
             ->setPost(array(
                 'name' => 'Sidney'
             ));
        $this->dispatch('/naked-php/call/type/service/object/Example_Model_PlaceFactory/method/createCity');
        $this->assertRedirectTo('/naked-php/edit/type/entity/object/1');
    }
}
