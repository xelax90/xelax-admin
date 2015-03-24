<?php

/*
 * Copyright (C) 2015 schurix
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace XelaxAdmin\Service;
use Zend\Mvc\Service\RouterFactory as DefaultRouterFactory;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Description of RouterFactory
 *
 * @author schurix
 */
class RouterFactory extends DefaultRouterFactory
{
    public function createService(ServiceLocatorInterface $serviceLocator, $cName = null, $rName = null)
    {
        $router = parent::createService($serviceLocator, $cName, $rName);

        //get instance of the RoutePluginManager
        $routePluginManager = $router->getRoutePluginManager();
        //set the ServiceLocator for the RoutePluginManager so we can use it in the route
        $routePluginManager->setServiceLocator( $serviceLocator );

        return $router;
    }
}