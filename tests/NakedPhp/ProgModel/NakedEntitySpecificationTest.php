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

class NakedEntitySpecificationTest extends NakedObjectSpecificationTest
{
    protected $_className = 'NakedPhp\ProgModel\NakedEntitySpecification';

    public function testIsNotAService()
    {
        $nc = new NakedEntitySpecification();
        $this->assertFalse($nc->isService());
    }

    public function testRetainsFieldsList()
    {
        $nc = new NakedEntitySpecification('', array(), $fields = array('Name', 'Role'));
        $this->assertEquals($fields, $nc->getAssociations());
    }

    public function testGivesAccessToAFieldByName()
    {
        $nc = new NakedEntitySpecification('', array(), array('key' => 'Name', 'Role'));
        $this->assertEquals('Name', $nc->getAssociation('key'));
    }
}
