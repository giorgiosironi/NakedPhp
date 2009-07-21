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

class NakedParamTest extends \PHPUnit_Framework_TestCase
{
    public function testRetainsTypeAndName()
    {
        $param = new NakedParam('array', 'info');
        $this->assertEquals('array', (string) $param->getType());
        $this->assertEquals('info', $param->getName());
    }

    public function testIsNotDefaultByDefault()
    {
        $param = new NakedParam('array', 'info');
        $this->assertFalse($param->getDefault());
    }
}
