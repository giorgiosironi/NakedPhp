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
 * @package    NakedPhp_ProgModel
 */

namespace NakedPhp\ProgModel;
use NakedPhp\MetaModel\NakedObjectSpecification;
use NakedPhp\ProgModel\Facet\CollectionArray;
use NakedPhp\ProgModel\Facet\Collection\TypeOfHardcoded;
use NakedPhp\Stubs\NakedObjectSpecificationStub;
use NakedPhp\Stubs\Phonenumber;
use NakedPhp\Test\Delegation;

class NakedBareObjectTest extends AbstractNakedObjectTest
{
    protected function _loadDelegation()
    {
        $specification = $this->getMock('NakedPhp\Stubs\NakedObjectSpecificationStub');
        $this->_object = new NakedBareObject(null, $specification);
        $this->_delegation = new Delegation($this, $specification);
    }

    public function testContainsItsWrappedObject()
    {
        $no = new NakedBareObject($this, null);
        $this->assertFalse($no->isWrapping(new \stdClass));
        $this->assertTrue($no->isWrapping($this));
    }

    public function testRetainsClassMetaModel()
    {
        $no = new NakedBareObject($this, $class = new NakedObjectSpecificationStub());
        $this->assertSame($class, $no->getSpecification());
    }

    public function testIsADecoratorForTheDomainObject()
    {
        $no = new NakedBareObject($this);
        $this->assertEquals('cannedResponse', $no->dummyMethod());
    }
    /** self-shunting */
    public function dummyMethod()
    {
        return 'cannedResponse';
    }

    public function testReturnsACommonStringRepresentationForUnconvertibleObjects()
    {
        $this->assertEquals('OBJECT', (string) new NakedBareObject(null, new NakedObjectSpecificationStub));
    }
    
    public function testReturnsNumberAndTypeOfElementsAsAStringRepresentationOfCollectionObjects()
    {
        $typeOfSpec = new NakedObjectSpecificationStub('stdClass');
        $typeOfFacet = new TypeOfHardcoded($typeOfSpec);
        $collectionFacet = new CollectionArray($typeOfFacet);
        $collectionSpec = new NakedObjectSpecificationStub('array');
        $collectionSpec->addFacet($typeOfFacet);
        $collectionSpec->addFacet($collectionFacet);
        $collection = new NakedBareObject(array(1, 2, 3), $collectionSpec);
        $this->assertEquals('3 stdClass', (string) $collection);
    }

    /**
     * @expectedException NakedPhp\ProgModel\Exception
     */
    public function testRaiseExceptionWhenUnexistentMethodIsCalled()
    {
        $this->_object->anUnexistentMethodWhichSurelyIsNotDefinedAnywhereInSubclassesAndOtherImplementations();
    }

    public function testUnwrapsTheWrappedEntity()
    {
        $no = new NakedBareObject($this);

        $this->assertSame($this, $no->getObject());
    }

    public function testIsEqualToAnotherInstanceThatWrapsTheSameEntity()
    {
        $no = new NakedBareObject($this);
        $equal = new NakedBareObject($this);
        $notEqual = new NakedBareObject(null);
        $this->assertTrue($no->equals($equal));
        $this->assertFalse($no->equals($notEqual));
    }

    public function testReturnsTheStateOfTheObject()
    {
        $no = new NakedBareObject($this, $class = new NakedObjectSpecificationStub('', array()));
        $class->setAssociations(array('nickname' => null));
        $this->assertEquals(array('nickname' => 'dummy'), $no->getState());
    }

    /**
     * FIX: NakedBareObject should not be used for scalar?
     * Think of a new adapter that implements NakedObject.
     */
    public function testSetsTheStateOfTheObject()
    {
        $data = array('nickname' => new NakedBareObject('dummy'));
        $field = $this->getMock('NakedPhp\ProgModel\OneToOneAssociation');
        $field->expects($this->once())
              ->method('setAssociation');
        $class = new NakedObjectSpecificationStub(null, array());
        $class->setAssociations(array('nickname' => $field));
        $no = new NakedBareObject(null, $class);
        $no->setState($data);
    }

    public function testSetsTheStateOfTheObjectAlsoWithARelation()
    {
        $data = array('phonenumber' => $phonenumber = new NakedBareObject(new Phonenumber));

        $field = $this->getMock('NakedPhp\ProgModel\OneToOneAssociation');
        $class = new NakedObjectSpecificationStub(null, array());
        $class->setAssociations(array('phonenumber' => $field));
        $no = new NakedBareObject(null, $class);

        $field->expects($this->once())
              ->method('setAssociation')
              ->with($no, $phonenumber);

        $no->setState($data);
    }

    public function testProxiesToTheClassForObtainingApplicableMethods()
    {
        $class = $this->getMock('NakedPhp\Stubs\NakedObjectSpecificationStub', array('getObjectActions'));
        $class->expects($this->any())
             ->method('getObjectActions')
             ->will($this->returnValue(array('dummy' => 'DummyMethod')));

        $no = new NakedBareObject($this, $class);
        $this->assertEquals(array('dummy' => 'DummyMethod'), $no->getObjectActions());
        $this->assertEquals('DummyMethod', $no->getObjectAction('dummy'));
        $this->assertTrue($no->hasObjectAction('dummy'));
        $this->assertFalse($no->hasObjectAction('notExistentMethodName'));
    }

    public function testProxiesToTheClassForFacetHolding()
    {
        $class = $this->getMock('NakedPhp\Stubs\NakedObjectSpecificationStub', array('getFacet'));
        $class->expects($this->once())
             ->method('getFacet')
             ->with('Dummy')
             ->will($this->returnValue('foo'));

        $no = new NakedBareObject(null, $class);
        $this->assertEquals('foo', $no->getFacet('Dummy'));
    }

    public function testIsTraversable()
    {
        $no = new NakedBareObject(null, null);
        $this->assertTrue($no instanceof \Traversable);
    }

    /**
     * @depends testReturnsTheStateOfTheObject
     */
    public function testIsTraversableProxyingToTheEntityState()
    {
        $class = new NakedObjectSpecificationStub('', array());
        $class->setAssociations(array('nickname' => null));
        $no = new NakedBareObject($this, $class);
        $this->assertEquals('dummy', $no->getIterator()->current());
    }

    /** self-shunting */
    public function getNickname()
    {
        return 'dummy';
    }

    public function testCreatesNewInstance()
    {
        $no = new NakedBareObject($this, null);
        $spec = new NakedObjectSpecificationStub();
        $new = $no->createNewInstance('dummy', $spec);

        $this->assertTrue($new instanceof NakedBareObject);
        $this->assertSame('dummy', $new->getObject());
        $this->assertSame($spec, $new->getSpecification());
    }
}
