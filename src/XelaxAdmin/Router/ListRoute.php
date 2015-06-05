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

namespace XelaxAdmin\Router;

use Zend\Mvc\Router\Http\RouteInterface;
use Zend\Mvc\Router\Http\RouteMatch;
use Zend\Mvc\Router\Exception;
use Zend\Stdlib\RequestInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use XelaxAdmin\Options\ListControllerOptions;


/**
 * Route to display Xelax List Controllers
 *
 * @author schurix
 */
class ListRoute implements RouteInterface, ServiceLocatorAwareInterface{
	
	/**
	 * @var ServiceLocatorInterface
	 */
	protected $routePluginManager;
	
	/**
	 * @var ListControllerOptions
	 */
	protected $controllerOptions;
	
	/**
	 * @var string
	 */
	protected $controllerOptionName;
	
	protected $assembledParams = array();
	
	protected $defaults = array();
	
	// all list actions
	protected $listActions = array('index','list', 'create', 'edit', 'delete', 'rest');
	
	// list actions which require the id to be set
	protected $listActionsWithId = array('edit', 'delete');
	
	// list actions with Optional id
	protected $listActionsWithOptinalId = array('rest');
	
	/**
	 * Create a new XelaxAdmin route
	 * @param string $route
	 * @param string $controllerOptionName
	 * @param array $constraints
	 * @param array $defaults
	 */
    public function __construct($controllerOptionName){
		$this->controllerOptionName = $controllerOptionName;
    }
	
	
    /**
     * match(): defined by RouteInterface interface.
     *
     * @see    \Zend\Mvc\Router\RouteInterface::match()
     * @param  Request     $request
     * @param  string|null $pathOffset
     * @param  array       $options
     * @return RouteMatch|null
     * @throws Exception\RuntimeException
     */
	public function match(RequestInterface $request, $pathOffset = null, array $options = array()) {
		$match = $this->match_part($request, $pathOffset, $options);
		if(!isset($match['params']['action'])){
			$match['params']['action'] = '';
		}
		if(!empty($match)){
			return new RouteMatch($match['params'], $match['length']);
		}
		return null;
	}
	
	protected function match_part($request, $pathOffset = null, array $options = array(), $routeName = null, $controllerOptions = null, $privilegeBase = null){
        if (!method_exists($request, 'getUri')) {
            return null;
        }
		if(empty($controllerOptions)){
			$controllerOptions = $this->getControllerOptions();
		}
		
		if(empty($routeName)){
			$routeName = $this->controllerOptionName;
		}
		
		if(empty($privilegeBase)){
			$privilegeBase = $this->controllerOptionName;
		}

        $uri  = $request->getUri();
        $path = $uri->getPath();
		if($pathOffset !== null){
			$toMatch = substr($path, $pathOffset);
		} else {
			$toMatch = $path;
		}
		
		$parts = explode('/', $toMatch);
		$curr = 0;
		if(empty($parts[$curr])){
			$curr++;
		}
		
		// check if base route is correct
		if($parts[$curr] !== $routeName){
			return null;
		}
		
		// check action
		$action = 'index';
		if(count($parts) >= $curr+2){ // if there are stil parts left
			if(in_array($parts[$curr+1], $this->listActions, true)){
				// a valid action is provided
				$action = $parts[$curr+1]; 
			} elseif($this->match_alias($parts[$curr+1])) {
				// no valid action provided, check if id was provided
				$action = 'sublist';
			} else {
				// if no id and no action provided, assume there is a child route
				$action = 'subroute';
			}
		}
		switch($action){
			case "edit":
			case "delete":
				$matchLength = strlen(implode("/", array_slice($parts, 0, $curr + 3)));
				$idAlias = $this->match_alias($parts[$curr+2]);
				return array(
					'params' => $this->getRouteParams($controllerOptions, $action, $idAlias['id'], $idAlias['alias'], $privilegeBase."/".$action),
					'length' => $matchLength,
				);
			case "rest":
				$matchLength = strlen(implode("/", array_slice($parts, 0, $curr+2)));
				$idAlias = false;
				if(!empty($parts[$curr+2])){
					$idAlias = $this->match_alias($parts[$curr+2]);
				}
				
				$method = strtolower($request->getMethod());
				$privilege = 'index';
				switch($method){
					case 'get' : $privilege = 'list'; break;
					case 'post' : 
						$privilege = 'create'; 
						if($idAlias){
							$privilege = 'edit';
						}
						break;
					case 'put' : $privilege = 'edit'; break;
					case 'delete' : $privilege = 'delete'; break;
				}
				$params = $this->getRouteParams($controllerOptions, '', 0, '', $privilegeBase."/".$privilege);
				if($idAlias){
					$matchLength = strlen(implode("/", array_slice($parts, 0, $curr+3)));
					$params = $this->getRouteParams($controllerOptions, '', $idAlias['id'], $idAlias['alias'], $privilegeBase."/".$privilege);
				}
				return array(
					'params' => $params,
					'length' => $matchLength,
				);
			case "sublist":
				$matchLength = strlen(implode("/", array_slice($parts, 0, $curr + 2)));
				$idAlias = $this->match_alias($parts[$curr+1]);
				$params = $this->getRouteParams($controllerOptions, 'index', $idAlias['id'], $idAlias['alias'], $privilegeBase."/index");
				
				$sublistParams = null;
				foreach($controllerOptions->getChildOptions() as $key => $childOption){
					$sublistParams = $this->match_part($request, $pathOffset+$matchLength, $options, $key, $childOption, $privilegeBase."/".$key);
					if($sublistParams !== null){
						break;
					}
				}
				if($sublistParams !== null){
					if(!isset($sublistParams['action'])){
						unset($params['action']);
					}
					$params = array_merge($params, $sublistParams['params']);
					$matchLength += $sublistParams['length'];
				}
				
				return array(
					'params' => $params,
					'length' => $matchLength
				);
				
			case "subroute":
				if($pathOffset === null){
					return null;
				}
				$matchLength = strlen(implode("/", array_slice($parts, 0, $curr + 1)));
				return array(
					'params' => $this->getRouteParams($controllerOptions),
					'length' => $matchLength,
				);
			default :
				// list, create and other actions
				$matchLength = strlen(implode("/", array_slice($parts, 0, $curr + 2)));
				return array(
					'params' => $this->getRouteParams($controllerOptions, $action, 0, '', $privilegeBase."/".$action),
					'length' => $matchLength,
				);
		}
		
	}
	
	protected function getRouteParams(ListControllerOptions $options, $action = 'index', $id = 0, $alias = '', $privilege = ''){
		$res = array(
			'controller' => $options->getControllerClass(),
			$options->getAliasParamName() => $alias,
			'xelax_admin_privilege' => $privilege,
		);
		if(!empty($id)){
			$res[$options->getIdParamName()] = $id;
		}
		if(!empty($action)){
			$res['action'] = $action;
		}
		return $res;
	}
	
	protected function match_alias($part){
		if(empty($part)){
			return false;
		}
		$parts = explode('-', $part);
		$id = array_pop($parts);
		$alias = implode('-', $parts);
		if(!is_numeric($id)){
			$parts = explode('-', $part, 2);
			$id = $parts[0];
			$alias = empty($parts[1]) ? "" : $parts[1];
			if(!is_numeric($parts[0])){
				return false;
			}
		}
		
		return array(
			'alias' => $alias,
			'id' => $id
		);
	}
	
	protected function buildRoute(array $params, array $options = array(), $routeName = null, ListControllerOptions $controllerOptions = null){
		if(empty($controllerOptions)){
			$controllerOptions = $this->getControllerOptions();
		}
		
		if(empty($routeName)){
			$routeName = $this->controllerOptionName;
		}
		
		if(empty($params['route'])){ // no route provided, set default
			$params['route'] = $routeName;
		}
		
		if($params["route"]{0} === "/"){
			$params["route"] = substr($params["route"], 1);
		}
		$parts = explode("/", $params["route"]);
		
		$res = array();
		
		if($parts[0] != $routeName){
			return false;
		}
		
		$res[] = $parts[0];
		
		if(!empty($parts[1]) && in_array($parts[1], $this->listActions)){
			// list action
			$res[] = $parts[1];
			
			if(in_array($parts[1], $this->listActionsWithId)){
				// list action with required id
				if(empty($params[$controllerOptions->getIdParamName()])){
					throw new Exception\RuntimeException("List action '".$params[1]."' requires an id.");
				}
				$res[] = $this->make_alias($controllerOptions, $params);
			}
			
			// list actions have no children
			return "/".implode("/", $res);
		}
		
		if(!empty($params[$controllerOptions->getIdParamName()])){
			$res[] = $this->make_alias($controllerOptions, $params);
		}
		
		if(!empty($parts[1])){
			// try to match child controller
			foreach($controllerOptions->getChildOptions() as $key => $child){
				$childRes = $this->buildRoute(array_merge($params, array("route" => implode("/",array_slice($parts, 1)))), $options, $key, $child);
				if(!empty($childRes)){
					// match found
					return "/".implode("/", $res).$childRes;
				}
			}
		}
		// no matching child, assuming subroute
		return "/".implode("/", $res);
	}
	
	
	protected function make_alias(ListControllerOptions $options, $params){
		if(empty($params[$options->getIdParamName()])){
			return false;
		}
		
		$idAlias = $params[$options->getIdParamName()];
		$this->assembledParams[] = $options->getIdParamName();
		if(!empty($params[$options->getAliasParamName()])){
			$idAlias .= "-".$params[$options->getAliasParamName()];
			$this->assembledParams[] = $options->getAliasParamName();
		}
		return $idAlias;
	}

	/**
     * assemble(): Defined by RouteInterface interface.
     *
     * @see    \Zend\Mvc\Router\RouteInterface::assemble()
     * @param  array $params
     * @param  array $options
     * @return mixed
     */
	public function assemble(array $params = array(), array $options = array()) {
		$this->assembledParams = array();
		return $this->buildRoute(array_merge($params, $this->defaults));
	}

	public function getAssembledParams() {
		return $this->assembledParams;
	}
	
	protected function checkRoute(){
		if(empty($this->parts)){
			$this->build2Route();
		}
	}
	
	protected function build2Route($options = null, $level = 0){
		if(empty($options)){
			$options = $this->getControllerOptions();
		}
		
		$route = '/'.strtolower($options->getName())."/:action".$level."[/:".$options->getIdParamName()."[-:".$options->getAliasParamName()."]]";
		$constraints = array(
			'action'.$level => 'list|create|edit|delete',
			$options->getIdParamName() => '[0-9]+',
			$options->getAliasParamName() => '[0-9a-zA-Z-]+',
		);
		
		$child_routes = array();
		if(!empty($options->getChildOptions())){
			foreach($options->getChildOptions() as $key => $option){
				$child_routes[] = $this->buildRoute($option, $level+1);
			}
		}
		
		
		
	}
	
	/**
     * getServiceLocator(): defined by ServiceLocatorAwareInterface interface.
	 * 
     * @see    ServiceLocatorAwareInterface::getServiceLocator()
     * @return ServiceLocatorInterface
	 */
	public function getServiceLocator() {
		return $this->routePluginManager;
	}

	/**
     * setServiceLocator(): defined by ServiceLocatorAwareInterface interface.
	 * 
     * @see    ServiceLocatorAwareInterface::setServiceLocator()
     * @param ServiceLocatorInterface $serviceLocator ServiceLocator
	 */
	public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
		$this->routePluginManager = $serviceLocator;
	}
	
	/**
     * Fetches and returns the associated ListControllerOptions for this route
	 * 
     * @return ListControllerOptions
     * @throws Exception\RuntimeException
	 */
	public function getControllerOptions(){
		if(empty($this->controllerOptions)){
			$routePluginManager = $this->getServiceLocator();

			if(empty($routePluginManager)){
				throw new Exception\RuntimeException('ServiceLocator not set');
			}

			/* @var $sl ServiceLocatorInterface */
			$sl = $routePluginManager->getServiceLocator();
			if(empty($sl)){
				throw new Exception\RuntimeException('Plugin manager ServiceLocator not set');
			}
			
			$config = $sl->get('XelaxAdmin\ListControllerOptions');
			
			if(empty($config[$this->controllerOptionName])){
				throw new Exception\RuntimeException('Controller options not found');
			}
			
			$this->controllerOptions = $config[$this->controllerOptionName];
		}
		return $this->controllerOptions;
	}

    /**
     * factory(): defined by RouteInterface interface.
     *
     * @see    \Zend\Mvc\Router\RouteInterface::factory()
     * @param  array|Traversable $options
     * @return XelaxAdmin
     * @throws Exception\InvalidArgumentException
     */
	public static function factory($options = array()) {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        } elseif (!is_array($options)) {
            throw new Exception\InvalidArgumentException(__METHOD__ . ' expects an array or Traversable set of options');
        }

        if (!isset($options['controller_options_name'])) {
            throw new Exception\InvalidArgumentException('Missing "controller_options_name" in options array');
        }
		
        return new static($options['controller_options_name']);
	}

}
