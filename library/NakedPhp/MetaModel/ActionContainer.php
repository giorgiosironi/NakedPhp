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
 * Wraps @see NakedObjectAction instances.
 */
interface ActionContainer
{
    /**
     * @return array $methods    NakedObjectAction instances; keys are method names
     */
    public function getObjectActions();

    /**
     * Convenience method.
     * @param string $name
     * @return NakedObjectAction
     */
    public function getObjectAction($name);

    /**
     * Convenience method.
     * @param string $name
     * @return boolean
     */
    public function hasMethod($name);
}
