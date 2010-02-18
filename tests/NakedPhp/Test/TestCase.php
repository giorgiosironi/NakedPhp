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
}
