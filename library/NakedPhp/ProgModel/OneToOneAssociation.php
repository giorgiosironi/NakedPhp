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
use NakedPhp\MetaModel\NakedObject;
use NakedPhp\MetaModel\NakedObjectAssociation;

/**
 * Wraps info about a field of a NakedObjectSpecification.
 * FIX: Move in ProgModel.
 */
class OneToOneAssociation extends AbstractFacetHolder implements NakedObjectAssociation
{
    /**
     * @var string
     */
    private $_type;

    /**
     * @var string
     */
    private $_id;

    /**
     * @param string $type
     * @param string $id    unambiguos identifier
     */
    public function __construct($type = '', $id = '')
    {
        $this->_type = $type;
        $this->_id = $id;
    }

    public function getType()
    {
        return $this->_type;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * FIX: use the PropertySetter Facet
     */
    public function setAssociation(NakedObject $inObject, NakedObject $associate)
    {
        $inObject = $inObject->getObject();
        $associate = $associate->getObject();
        $setter = 'set' . ucfirst($this->_id);
        $inObject->$setter($associate);
    }
}
