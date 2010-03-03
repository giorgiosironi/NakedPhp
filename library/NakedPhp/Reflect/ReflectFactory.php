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

namespace NakedPhp\Reflect;

class ReflectFactory
{
    protected $_specLoader;

    /**
     * @return SpecificationLoader
     */
    public function getSpecificationLoader($folder, $prefix)
    {
        if (!isset($this->_specLoader)) {
            $this->_specLoader = new PhpSpecificationLoader(
                new PhpIntrospectorFactory(
                    array(
                        new SpecificationFactory\PhpClassesSpecificationFactory(
                            new SpecificationFactory\FilteredClassDiscoverer(
                                new SpecificationFactory\FilesystemClassDiscoverer($folder, $prefix),
                                $docblockParser = new DocblockParser
                            )
                        ),
                        new SpecificationFactory\PhpTypesSpecificationFactory
                    ),
                    new FactoriesFacetProcessor(array(
                        new FacetFactory\PropertyMethodsFacetFactory,
                        new FacetFactory\ActionMethodsFacetFactory
                    )),
                    $progModelFactory = new ProgModelFactory(
                        new MethodsReflector(
                            $docblockParser
                        )
                    )
                )
            );
            $progModelFactory->initSpecificationLoader($this->_specLoader);
        }
        return $this->_specLoader;
    }
}
