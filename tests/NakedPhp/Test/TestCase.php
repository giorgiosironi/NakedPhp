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
 * @package    NakedPhp_Test
 */

namespace NakedPhp\Test;

/**
 * Base class for test cases that need the Delegation helper.
 */
class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * HACK: this is necessary to pass $this externally
     */
    public function once() { return parent::once(); }
    public function returnValue($value) { return parent::returnValue($value); }

    /**
     * <code>
     * $facet = $this->getFacetMock('Collection');
     * </code>
     * @return Facet
     */
    public function getFacetMock($facetBaseName)
    {
        $facet = $this->getMock('NakedPhp\MetaModel\Facet\\' . $facetBaseName);
        $facet->expects($this->any())
              ->method('facetType')
              ->will($this->returnValue($facetBaseName));
        return $facet;
    }

    /**
     * Assert against DOM selection
     * 
     * @param  string $path CSS selector path
     * @param  string $message
     * @return void
     */
    public function assertQuery($content, $path, $message = '')
    {
        $constraint = new \Zend_Test_PHPUnit_Constraint_DomQuery($path);
        $this->assertTrue($constraint->evaluate($content, __FUNCTION__));
    }

    /**
     * Assert against DOM selection; node should contain content
     * 
     * @param  string $path CSS selector path
     * @param  string $match content that should be contained in matched nodes
     * @param  string $message
     * @return void
     */
    public function assertQueryContentContains($content, $path, $match, $message = '')
    {
        $constraint = new \Zend_Test_PHPUnit_Constraint_DomQuery($path);
        $this->assertTrue($constraint->evaluate($content, __FUNCTION__, $match));
    }

    public function assertQueryContentNotContains($content, $path, $match, $message = '')
    {
        $constraint = new \Zend_Test_PHPUnit_Constraint_DomQuery($path);
        $this->assertTrue($constraint->evaluate($content, __FUNCTION__, $match));
    }
}
