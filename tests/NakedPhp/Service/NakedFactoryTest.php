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
use NakedPhp\Metadata\NakedBareEntity;
use NakedPhp\Metadata\NakedEntityClass;
use NakedPhp\Metadata\NakedService;
use NakedPhp\Metadata\NakedServiceClass;

class NakedFactoryTest extends \PHPUnit_Framework_TestCase
{
    private $_entityReflectorMock;
    private $_serviceReflectorMock;
    private $_factory;

    public function setUp()
    {
        $this->_entityReflectorMock = $this->getMock('NakedPhp\Reflect\EntityReflector', array('analyze'));
        $this->_serviceReflectorMock = $this->getMock('NakedPhp\Reflect\ServiceReflector', array('isService', 'analyze'));
        $this->_factory = new NakedFactory($this->_entityReflectorMock, $this->_serviceReflectorMock);
    }

    public function testWrapsAnEntityInANakedBareEntityInstance()
    {
        $this->_serviceReflectorMock->expects($this->any())
                                    ->method('isService')
                                    ->will($this->returnValue(false));
        $no = $this->_factory->create(new \stdClass);
        $this->assertTrue($no instanceof NakedBareEntity);
    }

    public function testGeneratesMetadataForEntities()
    {
        $this->_serviceReflectorMock->expects($this->any())
                                    ->method('isService')
                                    ->will($this->returnValue(false));
        $class = new NakedEntityClass();
        $this->_entityReflectorMock->expects($this->any())
                                    ->method('analyze')
                                    ->will($this->returnValue($class));
        $no = $this->_factory->create(new \stdClass);
        $this->assertSame($class, $no->getClass());
    }

    public function testWrapsAServiceInANakedServiceInstance()
    {
        $this->_serviceReflectorMock->expects($this->any())
                                    ->method('isService')
                                    ->will($this->returnValue(true));
        $no = $this->_factory->create(new \stdClass);
        $this->assertTrue($no instanceof NakedService);
    }

    public function testGeneratesMetadataForServices()
    {
        $this->_serviceReflectorMock->expects($this->any())
                                    ->method('isService')
                                    ->will($this->returnValue(true));
        $class = new NakedServiceClass();
        $this->_serviceReflectorMock->expects($this->any())
                                    ->method('analyze')
                                    ->will($this->returnValue($class));
        $no = $this->_factory->create(new \stdClass);
        $this->assertSame($class, $no->getClass());
    }

    public function testDoesNotWrapScalarValues()
    {
        $this->fail();
    }
}

