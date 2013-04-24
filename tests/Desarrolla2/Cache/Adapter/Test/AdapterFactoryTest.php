<?php

/**
 * This file is part of the Cache proyect.
 * 
 * Copyright (c)
 * Daniel González <daniel.gonzalez@freelancemadrid.es> 
 * 
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace Desarrolla2\Cache\Adapter\Test;

use Desarrolla2\Cache\Adapter\AdapterFactory;

/**
 * 
 * Description of AdapterFactoryTest
 *
 * @author : Daniel González <daniel.gonzalez@freelancemadrid.es> 
 * @file : AdapterFactoryTest.php , UTF-8
 * @date : Apr 24, 2013 , 11:45:35 PM
 */
class AdapterFactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * setup
     */
    public function setUp()
    {
        
    }

    public function getTest()
    {
        $noCache = AdapterFactory::get(array(
            
        ))
    }

}