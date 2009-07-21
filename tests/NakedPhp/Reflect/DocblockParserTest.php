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
 * @package    NakedPhp_Reflect
 */

namespace NakedPhp\Reflect;

class DocblockParserTest extends \PHPUnit_Framework_TestCase
{
    private $_parser;

    public function setUp()
    {
        $this->_parser = new DocblockParser();
    }

    public function testListsDocblockParamAnnotations()
    {
        $comment = <<<EOT
        /**
         * @param string \$text   a nice description
         *
         */
EOT;
        $result = $this->_parser->parse($comment);
        $this->assertEquals(array(
                                'annotation' => 'param',
                                'type' => 'string',
                                'name' => 'text',
                                'description' => 'a nice description'
                            ), 
                            $result[0]);
    }

    public function testListsDocblockReturnAnnotations()
    {
        $comment = <<<EOT
        /**
         * @return string   a nice description
         *
         */
EOT;
        $result = $this->_parser->parse($comment);
        $this->assertEquals(array(
                                'annotation' => 'return',
                                'type' => 'string',
                                'description' => 'a nice description'
                            ), 
                            $result[0]);
    }
}
