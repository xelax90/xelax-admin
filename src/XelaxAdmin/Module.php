<?php
/* 
 * Copyright (C) 2014 schurix
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

namespace XelaxAdmin;

use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceManager;
use XelaxAdmin\Options\ListControllerOptions;
use Zend\EventManager\EventManager;
use Zend\Mvc\Router\Http\TreeRouteStack;

class Module
{
    public function onBootstrap(MvcEvent $e){
		$app = $e->getApplication();
		/* @var $sm \Zend\ServiceManager\ServiceLocatorInterface */
		$sm = $app->getServiceManager();
		$this->addListControllerRoutes($app, $sm);
    }
	
	protected function addListControllerRoutes(\Zend\Mvc\ApplicationInterface $app, ServiceManager $sm){
		
		$options = $sm->get('XelaxAdmin\ListControllerOptions');
		
		if (empty($options)) {
			return;
		}
		
		// TODO: Allow arbitrary start position for top-level controllers
		foreach ($options as $name => $option){
			/* @var $router \Zend\Mvc\Router\TreeRouteStack */
			$router = $this->getRouterByRoute($sm, $name);
			
			$routeParts = explode('/', $name);
			$routeName = array_pop($routeParts);
			
			/* @var $option Options\ListControllerOptions */
			$route = $this->getListControllerRoute($name, $option);
			$router->addRoute($routeName, $route);
		}
	}
	
	protected function getRouterByRoute(ServiceManager $sm, $route){
		/* @var $router \Zend\Mvc\Router\TreeRouteStack */
		$router = $sm->get('router');
		
		$routeParts = explode('/', $route);
		$routeName = $route;
		
		if(count($routeParts) > 1){
			$routeName = array_pop($routeParts);
			foreach($routeParts as $routePart){
				$router = $router->getRoutes()->get($routePart);
				if(empty($router) || !$router instanceof TreeRouteStack){
					throw new Exception(sprintf('Router %s not found', implode('/', $routeParts)));
				}
			}
		}
		
		return $router;
	}
	
	protected function getListControllerRoute($routeBase, ListControllerOptions $options){
		if(empty($options->getName())){
			return null;
		}
		
		if(
			!empty($options->getListRoute()) &&
			!empty($options->getCreateRoute()) &&
			!empty($options->getDeleteRoute()) &&
			!empty($options->getEditRoute())
		){
			return null;
		}
		
		$route = array(
			'type' => 'literal',
			'options' => array(
				'route' => '/'.$options->getName(),
				'defaults' => array(
					'controller' => $options->getControllerClass(),
					'action'     => 'index',
				),
			),
		);
		
		$route['child_routes'] = array();
		
		if(!empty($options->getChildOptions())){
			$childOptions = $options->getChildOptions();
			foreach($childOptions as $childName => $childOption){
				// get child route
				$childRoute = $this->getListControllerRoute($routeBase.'/'.$childName, $childOption);
				// add parent id parameter
				$childRoute['type'] = 'Segment';
				$childRoute['options']['route'] = '/:'.$options->getChildRouteParamName().$childRoute['options']['route'];
				$childRoute['options']['defaults'][$options->getChildRouteParamName()] = 0;
				$childRoute['options']['constraints'][$options->getChildRouteParamName()] = '[0-9]*';

				// attach route
				$route['child_routes']['_'.$childName] = $childRoute;
			}
		}

		
		if(empty($options->getListRoute())){
			$route['child_routes']['list'] = array(
				'type' => 'Segment',
				'options' => array(
					'route' => '/list[/:p]',
					'defaults' => array(
						'controller' => $options->getControllerClass(),
						'action'     => 'list',
					),
					'constraints' => array(
						'p'         => '[0-9]*',
					),
				),
			);
		}
		
		if(empty($options->getCreateRoute())){
			$route['child_routes']['create'] = array(
				'type' => 'Literal',
				'options' => array(
					'route' => '/create',
					'defaults' => array(
						'controller' => $options->getControllerClass(),
						'action'     => 'create'
					),
				),
			);
		}
		
		if(empty($options->getEditRoute())){
			$route['child_routes']['edit'] = array(
				'type' => 'Segment',
				'options' => array(
					'route' => '/:'.$options->getEditParamName().'/edit',
					'defaults' => array(
						'controller' => $options->getControllerClass(),
						'action'     => 'edit',
						$options->getEditParamName() => 0
					),
					'constraints' => array(
						$options->getEditParamName() => '[0-9]+',
					),
				),
			);
		}
		
		if(empty($options->getDeleteRoute())){
			$route['child_routes']['delete'] = array(
				'type' => 'Segment',
				'options' => array(
					'route' => '/:'.$options->getDeleteParamName().'/delete',
					'defaults' => array(
						'controller' => $options->getControllerClass(),
						'action'     => 'delete',
						$options->getDeleteParamName() => 0
					),
					'constraints' => array(
						$options->getDeleteParamName() => '[0-9]+',
					),
				),
			);
		}
		
		// TODO: Custom child-routes
		// TODO: Configure which default routes should be visible
		// TODO: Configure routes for BjyAuthorize
		return $route;
	}
	
    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__,
                ),
            ),
        );
    }
}
