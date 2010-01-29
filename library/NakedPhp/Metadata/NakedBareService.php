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
 * Wraps a service object.
 */
class NakedBareService extends AbstractNakedObject implements NakedService
{
    public function __construct($service = null, NakedServiceSpecification $class = null)
    {
        parent::__construct($service, $class);
    }

    /**
     * @return NakedServiceSpecification
     */
    public function getSpecification()
    {
        return $this->_class;
    }
}

