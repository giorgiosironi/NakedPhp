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
    /**
     * @return SpecificationLoader
     */
    public function createSpecificationLoader($folder, $prefix)
    {
        $loader = new PhpSpecificationLoader(
            new PhpSpecificationFactory(
                new FilesystemClassDiscoverer($folder, $prefix)
            ),
            new PhpIntrospectorFactory(
                new FactoriesFacetProcessor(array(
                    new FacetFactory\PropertyMethodsFacetFactory,
                    new FacetFactory\ActionMethodsFacetFactory
                )),
                new ProgModelFactory(
                    new MethodsReflector(
                        new DocblockParser
                    )
                )
            )
        );
        return $loader;
    }

    /**
     * @return ServiceReflector
     */
    public function createServiceReflector()
    {
        $parser = new DocblockParser();
        $methodsReflector = $this->_createMethodsReflector();
        return new ServiceReflector($parser, $methodsReflector);
    }

    protected function _createMethodsReflector()
    {
        return new MethodsReflector(new DocblockParser());
    }
}
