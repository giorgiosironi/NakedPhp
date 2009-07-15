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
 */

class InstallationTest extends \PHPUnit_Framework_TestCase
{
    public function testZendFrameworkClassesAreAutoloaded()
    {
        $form = new \Zend_Form();
        $this->assertTrue($form instanceof \Zend_Form);
    }

    public function testDoctrineClassesAreAutoloaded()
    {
        $collection = new \Doctrine\Common\Collections\Collection();
        $this->assertTrue($collection instanceof \Doctrine\Common\Collections\Collection);
    }

    public function testNakedPHpClassesAreAutoloaded()
    {
        $reflector = null;
    }
}
