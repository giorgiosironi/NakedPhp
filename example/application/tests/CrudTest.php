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

/**
 * TODO: split this class in multiple test cases
 */
class Example_CrudTest extends Example_AbstractTest
{
    const URL_PLACEFACTORY = '/naked-php/view/type/service/object/Example_Model_PlaceFactory';

    const CSS_SESSION_BAR = '#nakedphp_session';
    const CSS_SESSION_BAR_ENTITY = '#nakedphp_session dt a';
    const CSS_SESSION_SAVE_BUTTON = '#nakedphp_session .button.save';
    const CSS_SESSION_CLEAR_BUTTON = '#nakedphp_session .button.clear';

    const CSS_METHOD = '#methods a';
    const CSS_EDIT_BUTTON = '#object .button.edit';
    const CSS_REMOVE_BUTTON = '#object .button.remove';

    public function testFactoryIsLoaded()
    {
        $this->dispatch(self::URL_PLACEFACTORY);
        $this->assertQueryContentContains(self::CSS_METHOD, 'createCity');
        $this->assertQueryContentContains(self::CSS_METHOD, 'createPlaceCategory');
        $this->assertQueryContentContains(self::CSS_METHOD, 'createPlace');
        $this->assertNotQuery(self::CSS_EDIT_BUTTON);
        $this->assertNotQuery(self::CSS_REMOVE_BUTTON);
        $this->assertNotQuery(self::CSS_SESSION_SAVE_BUTTON);
        $this->assertNotQuery(self::CSS_SESSION_CLEAR_BUTTON);
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
        $this->_createCity('Sidney');
        $this->assertRedirectTo('/naked-php/view/type/entity/object/1');

        $this->_newDispatch('/naked-php/view/type/entity/object/1');
        $this->assertQueryContentContains(self::CSS_SESSION_BAR, 'Sidney');
        $this->assertQueryContentContains('.nakedphp_entity.Example_Model_City .name',
                                          'Sidney');
        $this->assertQuery(self::CSS_EDIT_BUTTON);
        $this->assertQuery(self::CSS_REMOVE_BUTTON);
        $this->assertQuery(self::CSS_SESSION_SAVE_BUTTON);
        $this->assertQuery(self::CSS_SESSION_CLEAR_BUTTON);
    }

    /**
     * @depends testCityFactoryMethodRequiresName
     */
    public function testMultipleCitiesFactoryMethodCreatesAnArray()
    {
        $this->_newDispatch('/naked-php/call/type/service/object/Example_Model_PlaceFactory/method/createSomeCities');
        $this->assertRedirectTo('/naked-php/view/type/entity/object/1');

        $this->_newDispatch('/naked-php/view/type/entity/object/1');
        $this->assertQueryContentContains(self::CSS_SESSION_BAR, '4 Example_Model_City');
        $this->assertQueryContentContains('.nakedphp_collection.Example_Model_City tr td',
                                          'New York');
        $this->assertQueryContentContains('.nakedphp_collection.Example_Model_City tr td',
                                          'Madrid');
    }

    /**
     * @depends testMultipleCitiesFactoryMethodCreatesAnArray
     */
    public function testMultiplePlacesFactoryMethodCreatesAnArrayWhoseItemsFieldsAreDisplayed()
    {
        $this->_newDispatch('/naked-php/call/type/service/object/Example_Model_PlaceFactory/method/createSomePlaces');
        $this->assertRedirectTo('/naked-php/view/type/entity/object/1');

        $this->_newDispatch('/naked-php/view/type/entity/object/1');
        $this->assertQueryContentContains('.nakedphp_collection.Example_Model_Place tr td',
                                          'MacLaren\'s Pub');
        $this->assertQueryContentContains('.nakedphp_collection.Example_Model_Place tr td',
                                          'http://example.com');
    }
    
    /**
     * @depends testCityFactoryMethodCreatesCityInstance
     */
    public function testPlaceFactoryMethodWhichRequiresACityShowsASelect()
    {
        $this->_createCity('Milan');
        $this->_createCity('Turin');

        $this->_newDispatch('/naked-php/call/type/service/object/Example_Model_PlaceFactory/method/createPlaceFromCity');
        $this->assertQueryContentContains('form select[name="city"] option', 'Milan');
    }

    /**
     * @depends testCityFactoryMethodCreatesCityInstance
     */
    public function testPlaceFactoryMethodWhichRequiresACityConservesContext()
    {
        $this->_newDispatch('/naked-php/call/type/service/object/Example_Model_PlaceFactory/method/createPlaceFromCity');

        $this->_createCity('Milan');
        $this->assertRedirectTo('/naked-php/call/type/service/object/Example_Model_PlaceFactory/method/createPlaceFromCity');
    }

    public function testPlaceFactoryMethodWhichRequiresACityCreatesObject()
    {
        $this->_createCity('London');
        $this->_resetAll();
        $this->getRequest()
             ->setMethod('POST')
             ->setPost(array(
                'city' => 1
             ));
        $this->dispatch('/naked-php/call/type/service/object/Example_Model_PlaceFactory/method/createPlaceFromCity');
        $this->assertRedirectTo('/naked-php/view/type/entity/object/2');
    }

    /**
     * @depends testMultiplePlacesFactoryMethodCreatesAnArrayWhoseItemsFieldsAreDisplayed
     */
    public function testCollectionItemsAreReachableAsStandaloneObjects()
    {
        $this->_newDispatch('/naked-php/call/type/service/object/Example_Model_PlaceFactory/method/createSomePlaces');
        $this->_newDispatch('/naked-php/view/type/entity/object/1');
        $this->assertQueryContentContains('.nakedphp_collection.Example_Model_Place tr td a', 'Go');

        $this->_newDispatch('/naked-php/view/type/entity/object/1/field/1');
        $this->assertRedirectTo('/naked-php/view/type/entity/object/2');

        $this->_newDispatch('/naked-php/view/type/entity/object/2');
        $this->assertQueryContentContains(self::CSS_SESSION_BAR, 'MacLaren\'s Pub');
    }

    public function testObjectsAreNeverDuplicatedInTheSession()
    {
        $this->_createPlace();
        $this->_resetAll();
        $this->getRequest()
             ->setMethod('POST')
             ->setPost(array(
                'name' => 'Test'
             ));
        $this->dispatch('/naked-php/call/type/entity/object/1/method/giveMeAName');
        $this->assertRedirectTo('/naked-php/view/type/entity/object/1');

        $this->_newDispatch('/naked-php/view/type/entity/object/1');
        $this->assertQueryCount(self::CSS_SESSION_BAR_ENTITY, 1);
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
        $this->_createPlaceCategory('Disco');
        $this->assertRedirectTo('/naked-php/view/type/entity/object/1');

        $this->_newDispatch('/naked-php/view/type/entity/object/1');
        $this->assertQueryContentContains(self::CSS_SESSION_BAR, 'Disco');
        $this->assertQueryContentContains('.nakedphp_entity.Example_Model_PlaceCategory .name',
                                          'Disco');
    }

    /**
     * @depends testCityFactoryMethodCreatesCityInstance
     */
    public function testPlaceEditingDisplaysOtherEntitiesAsSelectable()
    {
        $this->_createPlace();
        $this->_createCity('Sidney');

        $this->_newDispatch('/naked-php/edit/type/entity/object/1');
        $this->assertQueryContentContains('.nakedphp_entity.Example_Model_Place select[name="city"] option', 'Sidney');
    }

    /**
     * @depends testCityFactoryMethodCreatesCityInstance
     */
    public function testPlaceEditingConservesContext()
    {
        $this->_createPlace();

        $this->_newDispatch('/naked-php/edit/type/entity/object/1');
        $this->assertQuery('#nakedphp_context li a');//[href="**"]');

        $this->_createCity('Sidney');
        $this->assertRedirectTo('/naked-php/edit/type/entity/object/1');
    }

    public function testCitiesAreSavedAndRetrievedFromStorage()
    {
        $this->_createCity('Lisbona');
        $this->_createCity('Barcellona');
        $this->_newDispatch('/naked-php/save');
        $this->assertQueryContentContains('body', 'saved!');
        $this->assertQueryContentContains('#object table tr.new td.number', '2');
         
        $this->_newDispatch('/naked-php/clear');
        $this->assertRedirectTo('/naked-php');

        $this->_newDispatch('/naked-php/call/type/service/object/Example_Model_PlaceFactory/method/findAllCities');
        $this->assertRedirectTo('/naked-php/view/type/entity/object/1');

        $this->_newDispatch('/naked-php/view/type/entity/object/1');
        $this->assertQueryContentContains(self::CSS_SESSION_BAR, '2 Example_Model_City');
        $this->assertQueryContentContains('.nakedphp_collection.Example_Model_City td', 'Lisbona');
        $this->assertQueryContentContains('.nakedphp_collection.Example_Model_City td', 'Barcellona');
    }

    /**
     * Depends on the other also because some cities must be in the storage.
     * @depends testCitiesAreSavedAndRetrievedFromStorage
     */
    public function testFindAllCitiesActionIsHiddenProgrammaticallyWhenThereAreNoCitiesSaved()
    {
        $this->_storeCities(array('Lisbona', 'Barcellona'));

        $this->_newDispatch(self::URL_PLACEFACTORY);
        $this->assertQueryContentContains(self::CSS_METHOD, 'findAllCities');

        $this->resetStorage();
        $this->_newDispatch(self::URL_PLACEFACTORY);
        $this->assertNotQueryContentContains(self::CSS_METHOD, 'findAllCities');
    }

    /**
     * @depends testCitiesAreSavedAndRetrievedFromStorage
     */
    public function testSingleElementsAreSavedWhenCollectionAreSaved()
    {
        $this->_storeCities(array('Lisbona', 'Barcellona'));
        $this->_newDispatch('/naked-php/clear');
        $this->_newDispatch('/naked-php/call/type/service/object/Example_Model_PlaceFactory/method/findAllCities');
        $this->_newDispatch('/naked-php/save');
        $this->assertQueryContentContains('#object table tr.updated td.number', 2);
    }
    
    private function _storeCities(array $cities)
    {
        foreach ($cities as $cityName) {
            $this->_createCity($cityName);
        }
        $this->_newDispatch('/naked-php/save');
    }
}
