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

namespace NakedPhp\MetaModel\Facet\Property;
use NakedPhp\MetaModel\Facet;
use NakedPhp\MetaModel\NakedObject;

interface Validate extends Facet
{
    /**
     * @param NakedObject $no
     * @param mixed $proposedValue
     * @return string   reason of non valid result, or a bool to indicate
     *                  validation without messages
     */
    public function invalidReason(NakedObject $no, $proposedValue);
}
