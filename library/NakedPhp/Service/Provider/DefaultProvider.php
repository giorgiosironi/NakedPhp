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
 * @package    NakedPhp_Service
 */

namespace NakedPhp\Service\Provider;
use NakedPhp\ProgModel\NakedBareObject;
use NakedPhp\Service\ServiceProvider;

/**
 * Decorator of another ServiceProvider.
 * Performs automatic injection of EntityManager when
 * the Service is a subclass of a certain abstract class.
 */
class DefaultProvider implements ServiceProvider
{
    const BASE_REPOSITORY = 'NakedPhp\Storage\AbstractFactoryAndRepository';

    protected $_decorated;
    protected $_em;

    public function __construct(ServiceProvider $decorated = null,
                                \Doctrine\ORM\EntityManager $em = null)
    {
        $this->_decorated = $decorated;
        $this->_em        = $em;
    }

    public function getService($name)
    {
        if (is_subclass_of($name, self::BASE_REPOSITORY)) {
            $object = new $name($this->_em);
            $specifications = $this->getServiceSpecifications();
            $spec = $specifications[$name];
            return new NakedBareObject($object, $spec);
        } else {
            return $this->_decorated->getService($name);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getServiceSpecifications()
    {
        return $this->_decorated->getServiceSpecifications();
    }
}
