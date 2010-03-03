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

namespace NakedPhp\ProgModel\Facet\Action;
use NakedPhp\ProgModel\NakedBareObject;
use NakedPhp\Stubs\NakedObjectSpecificationStub;

class InvocationMethodTest extends \PHPUnit_Framework_TestCase
{
    public function testReturnsRightFacetType()
    {
        $facet = new InvocationMethod('myAction');
        $this->assertEquals('Action\Invocation', $facet->facetType());
    }

    public function testInvokesTheHookMethodAndWrapsTheResult()
    {
        $no = new NakedBareObject($this);
        $returnType = new NakedObjectSpecificationStub();
        $facet = new InvocationMethod('myAction', $returnType);

        $result = $facet->invoke($no, array('foo', 'bar'));

        $this->assertEquals('dummy', $result->getObject());
        $this->assertSame($returnType, $result->getSpecification());
    }
    
    public function myAction($param, $otherParam)
    {
        $this->assertEquals('foo', $param);
        $this->assertEquals('bar', $otherParam);
        return 'dummy';
    }

    public function testHasAStringRepresentationEqualToTheMethodName()
    {
        $facet = new InvocationMethod('myAction');
        $this->assertEquals('myAction', (string) $facet);
    }
}
