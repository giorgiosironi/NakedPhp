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

class ContextContainerTest extends \PHPUnit_Framework_TestCase
{
    private $_container;

    public function setUp()
    {
        $this->_container = new ContextContainer();
    }

    public function testRemembersInsertedUrl()
    {
        $this->_container->remember('/edit/1');

        $this->assertEquals(1, count($this->_container));
        foreach ($this->_container as $url) {
            $this->assertEquals('/edit/1', $url);
        }
    }

    /**
     * @depends testRemembersInsertedUrl
     */
    public function testAfterCompletionDeletesLastUrlInserted()
    {
        $this->_container->remember('/edit/1');
        $this->_container->remember('/search-objects');
        $this->_container->completed();
        $this->assertEquals(1, count($this->_container));
    }
}
