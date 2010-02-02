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
 * @package    NakedPhp_MetaModel
 */

namespace NakedPhp\MetaModel\Facet\Action;
use NakedPhp\ProgModel\NakedBareObject;

class InvocationTest extends \PHPUnit_Framework_TestCase
{
    public function testReturnsRightFacetType()
    {
        $facet = new Invocation('myAction');
        $this->assertEquals('Action\Invocation', $facet->facetType());
    }

    public function testInvokesTheHookMethod()
    {
        $no = new NakedBareObject($this);
        $facet = new Invocation('myAction');
        $result = $facet->invoke($no, array('foo', 'bar'));
        $this->assertEquals('dummy', $result);
    }
    
    public function myAction($param, $otherParam)
    {
        $this->assertEquals('foo', $param);
        $this->assertEquals('bar', $otherParam);
        return 'dummy';
    }

    public function testHasAStringRepresentationEqualToTheMethodName()
    {
        $facet = new Invocation('myAction');
        $this->assertEquals('myAction', (string) $facet);
    }
}
