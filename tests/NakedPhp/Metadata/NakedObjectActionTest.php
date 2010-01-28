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
 * @package    NakedPhp_Metadata
 */

namespace NakedPhp\Metadata;

class NakedObjectActionTest extends \PHPUnit_Framework_TestCase
{
    public function testRetainsId()
    {
        $method = new NakedObjectAction('doSomething');
        $this->assertEquals('doSomething', $method->getId());
    }

    public function testRetainsParamsList()
    {
        $method = new NakedObjectAction('doSomething', $params = array('one', 'two'));
        $this->assertEquals($params, $method->getParams());
    }

    public function testRetainsReturnType()
    {
        $method = new NakedObjectAction('doSomething', $params = array(), 'string');
        $this->assertEquals('string', $method->getReturn());
    }

    public function testImplementsFacetHolderInterface()
    {
        $field = new NakedObjectAction();
        $helper = new \NakedPhp\Test\FacetHolder($this);
        $helper->testIsFacetHolder($field);
    }
}
