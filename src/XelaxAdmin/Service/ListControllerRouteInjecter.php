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

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use XelaxAdmin\Options\ListControllerOptions;


class ListControllerRouteInjecter implements ServiceLocatorAwareInterface {
	/**
	 * @var ServiceLocatorInterface
	 */
	protected $sm;
	
	/**
	 * @return ListControllerRouteInjecter
	 */
	public function __invoke() {
		return new static();
	}
	
	public function addListControllerRoutes(){
		$sm = $this->getServiceLocator();
		
		$options = $sm->get('XelaxAdmin\ListControllerOptions');
		
		if (empty($options)) {
			return;
		}
		
		// TODO: Allow arbitrary start position for top-level controllers
		foreach ($options as $name => $option){
			/* @var $router \Zend\Mvc\Router\TreeRouteStack */
			$router = $this->getRouterByRoute($name);
			
			$routeParts = explode('/', $name);
			$routeName = array_pop($routeParts);
			
			/* @var $option Options\ListControllerOptions */
			$route = $this->getListControllerRoute($name, $option);
			if(!empty($route)){
				$router->addRoute($routeName, $route);
			}
		}
	}
	
	protected function getRouterByRoute($route){
		$sm = $this->getServiceLocator();
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
	
	protected function routeNeedsAutogenerate(\XelaxAdmin\Options\ListControllerRoute $route){
		if(empty($route)){
			return false;
		}
		
		if($route->getDisabled()){
			return false;
		}
		
		if(!empty($route->getRoute())){
			return false;
		}
		
		return true;
	}


	protected function getListControllerRoute($routeBase, ListControllerOptions $options){
		if(empty($options->getName())){
			return null;
		}
		
		if( 
			!$this->routeNeedsAutogenerate($options->getListRoute()) &&
			!$this->routeNeedsAutogenerate($options->getCreateRoute()) &&
			!$this->routeNeedsAutogenerate($options->getDeleteRoute()) &&
			!$this->routeNeedsAutogenerate($options->getEditRoute())
		){
			return null;
		}
		
		$route = $this->getLiteralConfig('/'.$options->getName(), $options->getControllerClass(), 'index');
		
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

		
		if($this->routeNeedsAutogenerate($options->getListRoute())){
			$route['child_routes']['list'] = $this->getSegmentConfig('/list[/:p]', $options->getControllerClass(), 'list', array(), array('p' => '[0-9]*'));
			$options->getListRoute()->setRoute($routeBase.'/'.'list');
		}
		
		if($this->routeNeedsAutogenerate($options->getCreateRoute())){
			$route['child_routes']['create'] = $this->getLiteralConfig('/create', $options->getControllerClass(), 'create');
			$options->getCreateRoute()->setRoute($routeBase.'/'.'create');
		}
		
		if($this->routeNeedsAutogenerate($options->getEditRoute())){
			$route['child_routes']['edit'] = $this->getSegmentConfig('/:'.$options->getEditParamName().'/edit', $options->getControllerClass(), 'edit', array($options->getEditParamName() => 0), array($options->getEditParamName() => '[0-9]+'));
			$options->getEditRoute()->setRoute($routeBase.'/'.'edit');
		}
		
		if($this->routeNeedsAutogenerate($options->getDeleteRoute())){
			$route['child_routes']['delete'] = $this->getSegmentConfig('/:'.$options->getDeleteParamName().'/delete', $options->getControllerClass(), 'delete', array($options->getDeleteParamName() => 0), array($options->getDeleteParamName() => '[0-9]+'));
			$options->getDeleteRoute()->setRoute($routeBase.'/'.'delete');
		}
		
		// TODO: Custom child-routes
		// TODO: Configure which default routes should be visible
		return $route;
	}
	
	protected function getLiteralConfig($route, $controller, $action){
		return array(
			'type' => 'Literal',
			'options' => array(
				'route' => $route,
				'defaults' => array(
					'controller' => $controller,
					'action'     => $action
				),
			),
		);
	}
	
	protected function getSegmentConfig($route, $controller, $action, $defaults, $constraints){
		$config = $this->getLiteralConfig($route, $controller, $action);
		$config['type'] = 'Segment';
		$config['options']['defaults'] = array_merge($config['options']['defaults'], $defaults);
		$config['options']['constraints'] = $constraints;
		return $config;
	}

	public function getServiceLocator() {
		return $this->sm;
	}

	public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
		$this->sm = $serviceLocator;
	}

}