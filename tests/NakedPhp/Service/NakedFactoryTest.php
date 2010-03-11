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
 * @package    NakedPhp_Service
 */

namespace NakedPhp\Service;
use NakedPhp\MetaModel\NakedObject;
use NakedPhp\Stubs\NakedObjectSpecificationStub;

class NakedFactoryTest extends \PHPUnit_Framework_TestCase
{
    private $_spec;
    private $_specificationLoaderMock;
    private $_factory;

    public function setUp()
    {
        $this->_spec = new NakedObjectSpecificationStub();
        $this->_specificationLoaderMock = $this->getMock('NakedPhp\Reflect\SpecificationLoader');
        $this->_factory = new NakedFactory($this->_specificationLoaderMock);
    }

    public function testWrapsAnEntityInANakedObjectInstance()
    {
        $no = $this->_factory->createBare(new \stdClass);
        $this->assertTrue($no instanceof NakedObject);
    }

    /**
     * @depends testWrapsAnEntityInANakedObjectInstance
     */
    public function testInsertsSpecification()
    {
        $this->_specificationLoaderMock->expects($this->once())
                                       ->method('loadSpecification')
                                       ->will($this->returnValue($this->_spec));
        $no = $this->_factory->createBare(new \stdClass);
        $this->assertSame($this->_spec, $no->getSpecification());
    }

    /**
     * TODO: will wrap arrays when the type is provided
     * A NakedObjectSpecification $suggestedType parameter may be useful?
     * This should not be necessary: arrays are wrapped via prototyping.
     */
    public function testWrapsAnArrayOfEntities()
    {
        $this->markTestIncomplete();
    }

    public function testWrapsAlsoScalarValues()
    {
        $result = $this->_factory->createBare('scalar result');
        $this->assertTrue($result instanceof NakedObject);
        $this->assertEquals('scalar result', $result->getObject());
    }

    /**
     * @depends testWrapsAlsoScalarValues
     */
    public function testInsertsSpecificationUsingTypeName()
    {
        $this->_specificationLoaderMock->expects($this->once())
                                       ->method('loadSpecification')
                                       ->with('string')
                                       ->will($this->returnValue($this->_spec));

        $result = $this->_factory->createBare('scalar result');
        $this->assertSame($this->_spec, $result->getSpecification());
    }
}

