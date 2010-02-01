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
 * @package    NakedPhp_MetaModel
 */

namespace NakedPhp\MetaModel;

/**
 * Wraps properties about a domain class.
 * @abstract    not declared abstract to allow testing of base functionality
 */
interface NakedObjectSpecification extends FacetHolder, ActionContainer, AssociationContainer
{
    /**
     * @return string   the fully qualified class name
     */
    public function getClassName();

    public function __toString();

    /**
     * Whether the object is a service with a single instance and should be 
     * "globally" available to the user in the interface.
     * @return boolean
     */
    public function isService();
}

