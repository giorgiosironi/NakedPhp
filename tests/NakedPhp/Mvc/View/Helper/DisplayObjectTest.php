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
 * @package    NakedPhp_Mvc
 */

namespace NakedPhp\Mvc\View\Helper;
use NakedPhp\Stubs\NakedObjectStub;
use NakedPhp\Stubs\NakedObjectSpecificationStub;
use NakedPhp\Stubs\View;
use NakedPhp\ProgModel\NakedBareObject;
use NakedPhp\ProgModel\OneToOneAssociation;
use NakedPhp\ProgModel\Facet\HiddenMethod;
use NakedPhp\ProgModel\Facet\CollectionArray;
use NakedPhp\ProgModel\Facet\Collection\TypeOfHardcoded;

class DisplayObjectTest extends \NakedPhp\Test\TestCase
{
    private $_helper;
    private $_object;

    public function setUp()
    {
        $this->_object = new NakedObjectStub($this);
        $this->_object->setState(array(
            'firstName' => 'Giorgio',
            'lastName' => 'Sironi'
        ));
        $this->_object->setField('firstName', new OneToOneAssociation('string', 'firstName'));
        $this->_object->setField('lastName', new OneToOneAssociation('string', 'lastName'));
        $this->_helper = new DisplayObject();
    }

    public function testProducesHtmlTableWhereRowsAreAssociationOfTheObject()
    {
        $this->_object->setClassName('DummyClass');
        $result = $this->_helper->displayObject($this->_object);
        $this->assertQuery($result, 'table.nakedphp_entity.DummyClass');
        $this->assertQuery($result, 'table tr');

        $this->assertQueryContentContains($result, 'table tr td', 'firstName');
        $this->assertQueryContentContains($result, 'table tr td', 'Giorgio');
        $this->assertQueryContentContains($result, 'table tr td', 'lastName');
        $this->assertQueryContentContains($result, 'table tr td', 'Sironi');
    }

    public function testHidesFieldsProgrammatically()
    {
        $hiddenFacet = $this->getFacetMock('Hidden');
        $hiddenFacet->expects($this->any())
                    ->method('hiddenReason')
                    ->will($this->returnValue(true));
        $this->_object->getAssociation('firstName')->addFacet($hiddenFacet);
        $result = $this->_helper->displayObject($this->_object);

        $this->assertQueryContentNotContains($result, 'table tr td', 'firstName');
    }

    public function testDisplaysACollectionAsATableWhereRowsAreElements()
    {
        $collectionFacet = $this->getFacetMock('Collection');
        $collectionFacet->expects($this->once())
                        ->method('iterator')
                        ->will($this->returnValue(new \ArrayIterator(array(
                            $this->_object
                        ))));
        $typeOfFacet = $this->getFacetMock('Collection\TypeOf');
        $typeOfFacet->expects($this->once())
                    ->method('valueSpec')
                    ->will($this->returnValue(new NakedObjectSpecificationStub('My_Item_Class')));
        $collection = new NakedObjectStub();
        $collection->addFacet($collectionFacet);
        $collection->addFacet($typeOfFacet);

        $this->_injectFakeUrlHelper();
        $result = $this->_helper->displayObject($collection);

        $this->assertQuery($result, 'table.nakedphp_collection.My_Item_Class');
        $this->assertQueryContentContains($result, 'table.nakedphp_collection.My_Item_Class tr td', 'Giorgio');
        $this->assertQueryContentContains($result, 'table.nakedphp_collection.My_Item_Class tr td a', 'Go');
    }

    public function testDisplaysFieldTypeWhenNotConvertibleToString()
    {
        $this->_object->setState(array('firstName' => new \stdClass));
        $result = $this->_helper->displayObject($this->_object);
        $this->assertQueryContentContains($result, 'table tr td', 'stdClass');
    }

    private function _injectFakeUrlHelper()
    {
        $stubView = new View;
        $this->_helper->setView($stubView);
        $urlHelperMock = $this->getMock('Zend_View_Helper_Url');
        $stubView->setHelper('url', $urlHelperMock);
        $urlHelperMock->expects($this->any())
                      ->method('url')
                      ->will($this->returnValue('/stub'));
    }
}
