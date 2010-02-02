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

/**
 * Wraps properties about a service domain class.
 * A service is defined as a stateless object, with a bunch of methods
 * that takes as parameters scalars and entities or value objects.
 * Other services should be required in the constructor.
 */
class NakedServiceSpecification extends AbstractNakedObjectSpecification
{
    /**
     * {@inheritdoc}
     */
    public function getAssociations()
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function getAssociation($name)
    {
        throw new Exception("Attempting to access field $name over a service object.");
    }

    /**
     * {@inheritdoc}
     */
    public function isService()
    {
        return true;
    }
}
