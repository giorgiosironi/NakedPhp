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
 * @package    NakedPhp_Storage
 */

namespace NakedPhp\Storage;
use NakedPhp\MetaModel\NakedObject;
use NakedPhp\Mvc\EntityContainer\StateDiscoverer;

class DoctrineStateDiscoverer implements StateDiscoverer
{
    private $_em;

    public function __construct(\Doctrine\ORM\EntityManager $em = null)
    {
        $this->_em = $em;
    }

    public function isTransient(NakedObject $no)
    {
        if (($collectionFacet = $no->getFacet('Collection')) !== null) {
            $transient = true;
            foreach ($collectionFacet->iterator($no) as $item) {
                $entity = $item->getObject();
                if ($this->_em->contains($entity)) {
                    $transient = false;
                }
            }
        } else {
            $entity = $no->getObject();
            $transient = !$this->_em->contains($entity);
        }
        return $transient;
    }
}

