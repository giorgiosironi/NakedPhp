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

/**
 * Wraps properties about a entity class like fields, methods and metadata.
 * An entity is defined as a stateful class.
 */
class NakedEntitySpecification extends AbstractNakedObjectSpecification
{
    /**
     * @var array of OneToOneAssociation instances
     */
    protected $_fields;

    /**
     * @param string $className
     * @param array $methods
     * @param array $fields
     */
    public function __construct($className = '', array $methods = array(), array $fields = array())
    {
        parent::__construct($className, $methods);
        $this->_fields = $fields;
    }

    /**
     * {@inheritdoc}
     */
    public function getFields()
    {
        return $this->_fields;
    }

    /**
     * {@inheritdoc}
     */
    public function getField($name)
    {
        return $this->_fields[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function isService()
    {
        return false;
    }
}
