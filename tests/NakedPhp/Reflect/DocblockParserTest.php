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

    public function testRecognizesContainedAnnotations()
    {
        $docblock = <<<EOT
        /**
         * @NakedDummyAnn
         * @OtherAnn
         */
EOT;
        $this->assertTrue($this->_parser->contains('NakedDummyAnn', $docblock));
        $this->assertTrue($this->_parser->contains('OtherAnn', $docblock));
        $this->assertFalse($this->_parser->contains('NotContained', $docblock));
    }

    public function testListsDocblockParamAnnotations()
    {
        $comment = <<<EOT
        /**
         * @param string \$text   a nice description
         *
         */
EOT;
        $result = $this->_parser->getPhpdocAnnotations($comment);
        $this->assertEquals(array(
                                'annotation' => 'param',
                                'type' => 'string',
                                'name' => 'text',
                                'description' => 'a nice description'
                            ), 
                            $result[0]);
    }

    public function testListsDocblockParamAnnotationsWithEmptyDescriptions()
    {
        $comment = <<<EOT
        /**
         * @param string \$text
         *
         */
EOT;
        $result = $this->_parser->getPhpdocAnnotations($comment);
        $this->assertEquals(array(
                                'annotation' => 'param',
                                'type' => 'string',
                                'name' => 'text',
                                'description' => ''
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
        $result = $this->_parser->getPhpdocAnnotations($comment);
        $this->assertEquals(array(
                                'annotation' => 'return',
                                'type' => 'string',
                                'description' => 'a nice description'
                            ), 
                            $result[0]);
    }

    public function testListsDocblockReturnAnnotationsWithoutDescription()
    {
        $comment = <<<EOT
        /**
         * @return string
         *
         */
EOT;
        $result = $this->_parser->getPhpdocAnnotations($comment);
        $this->assertEquals(array(
                                'annotation' => 'return',
                                'type' => 'string',
                                'description' => ''
                            ), 
                            $result[0]);
    }

    public function testFindsProperAnnotationsWhichAreNotMeantForPhpDocumentor()
    {
        $docblock = <<<EOT
        /**
         * @NakedDummyAnn
         * @OtherAnn
         */
EOT;
        $annotations = $this->_parser->getNakedPhpAnnotations($docblock);
        $this->assertEquals(array('NakedDummyAnn' => true, 'OtherAnn' => true),
                            $annotations);
    }

    public function testFindsProperAnnotationsWhichAreNotMeantForPhpDocumentorAndSavesTheirParameters()
    {
        $docblock = <<<EOT
        /**
         * @NakedDummyAnn(paramOne=20)
         * @OtherAnn
         */
EOT;
        $annotations = $this->_parser->getNakedPhpAnnotations($docblock);
        $this->assertEquals(array(
                                'NakedDummyAnn' => array(
                                    'paramOne' => 20
                                ),
                                'OtherAnn' => true
                            ),
                            $annotations);
    }

    public function testFindsProperAnnotationsWhichAreNotMeantForPhpDocumentorAndSavesTheirUnnamedParameters()
    {
        $docblock = <<<EOT
        /**
         * @NakedDummyAnn(className)
         */
EOT;
        $annotations = $this->_parser->getNakedPhpAnnotations($docblock);
        $this->assertEquals(array(
                                'NakedDummyAnn' => array(
                                    'className'
                                )
                            ),
                            $annotations);
    }
}
