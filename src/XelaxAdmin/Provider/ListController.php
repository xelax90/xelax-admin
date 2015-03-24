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

namespace XelaxAdmin\Provider;

use BjyAuthorize\Provider\Rule\ProviderInterface as RuleProvider;
use BjyAuthorize\Provider\Resource\ProviderInterface as ResourceProvider;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use XelaxAdmin\Options\ListControllerOptions;

/**
 * Rule and Resource provider for ListControllers
 */
class ListController implements RuleProvider, ResourceProvider, ServiceLocatorAwareInterface
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $sl;
	
	/**
	 * @var array
	 */
	protected $rules;
	
    /**
	 * @param array $config
     */
    public function __construct($config = array()){
		
    }
	
	/**
	 * @return ServiceLocatorInterface
	 */
	public function getServiceLocator(){
		return $this->sl;
	}
	
	public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
		$this->sl = $serviceLocator;
	}
	
	/**
     * {@inheritDoc}
     */
	public function getRules(){
		if ($this->rules !== null) {
			return $this->rules;
		}
		
		$options = $this->getServiceLocator()->get('XelaxAdmin\ListControllerOptions');
		$rules = array();
		foreach ($options as $name => $option){
			/* @var $option Options\ListControllerOptions */
			
			// get XelaxAdmin route name
			if(!empty($option->getRouteBase())){
				$route = $option->getRouteBase();
			} else {
				$route = $name;
			}
			$rules = array_merge($rules, $this->getRulesController($route, $name, $option)['allow']);
		}
		$this->rules = array('allow' => $rules);
		return $this->rules;
	}
	
	public function getRulesController($route, $privilegeBase, ListControllerOptions $options){
		$rules = array();
		$childRoutes = array('index', 'list', 'create', 'edit', 'delete');
		foreach($childRoutes as $childRoute){
			$getter = 'get'.ucfirst($childRoute).'Route';
			if($childRoute == 'index'){
				$getter = 'getListRoute';
			}
			/* @var $route \XelaxAdmin\Options\ListControllerRoute */
			$routeOptions = $options->$getter();
			if(!$routeOptions->getDisabled()){
				// [ ['group1', 'group2', ...], 'resource name' ]
				$rules[] = [$routeOptions->getAllowedRoles(), 'xelax-route/'.$route, $privilegeBase."/".$childRoute];
			}
		}
		
		// check child options
		if(!empty($options->getChildOptions())){
			foreach($options->getChildOptions() as $name => $childOption){
				$childRules = $this->getRulesController($route, $privilegeBase."/".$name, $childOption);
				$rules = array_merge($rules, $childRules['allow']);
			}
		}
		
		return array('allow' => $rules);
	}

	public function getResources() {
		$rules = $this->getRules();
		$resources = array();
		foreach($rules['allow'] as $rule){
			$resources[] = $rule[1];
		}
		return $resources;
	}

}
