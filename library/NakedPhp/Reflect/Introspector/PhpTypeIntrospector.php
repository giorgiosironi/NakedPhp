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

namespace NakedPhp\Reflect\Introspector;
use NakedPhp\Reflect\Introspector;
use NakedPhp\ProgModel\PhpSpecification;

/**
 * Fills in a type Specification with emtpy values.
 */
class PhpTypeIntrospector implements Introspector
{
    protected $_specification;

    public function __construct(PhpSpecification $specification = null)
    {
        $this->_specification = $specification;
    }

    /**
     * {@inheritdoc}
     */
    public function getSpecification()
    {
        return $this->_specification;
    }

    /**
     * No-op.
     * @return void
     */
    public function introspectClass()
    {
    }

    /**
     * Initializes the associations list to empty.
     * @return void
     */
    public function introspectAssociations()
    {
        $this->_specification->initAssociations(array());
    }

    /**
     * Initializes the actions list to empty.
     * @return void
     */
    public function introspectActions()
    {
        $this->_specification->initObjectActions(array());
    }
}
