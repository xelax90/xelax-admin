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
use XelaxAdmin\Options\ListControllerOptions;

/**
 * Rule provider for ListControllers
 */
class ListController implements RuleProvider, ResourceProvider
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
     * @param ServiceLocatorInterface $sl
     */
    public function __construct($config, ServiceLocatorInterface $sl)
    {
        $this->sl = $sl;
    }
	
	/**
	 * @return ServiceLocatorInterface
	 */
	public function getServiceLocator(){
		return $this->sl;
	}
	
	/**
     * {@inheritDoc}
     */
    public function getRules($routeBase = '/', ListControllerOptions $options = null)
    {
		if ($this->rules !== null) {
			return $this->rules;
		}
		
		if($options === null){
			$options = $this->getServiceLocator()->get('XelaxAdmin\ListControllerOptions');
			$rules = array();
			foreach ($options as $name => $option){
				/* @var $option Options\ListControllerOptions */
				$rules = array_merge($rules, $this->getRules('/'.$name, $option)['allow']);
			}
			$this->rules = array('allow' => $rules);
			return $this->rules;
		}
		
		$rules = array();
		$childRoutes = array('list', 'create', 'edit', 'delete');
		foreach($childRoutes as $childRoute){
			$getter = 'get'.ucfirst($childRoute).'Route';
			/* @var $route \XelaxAdmin\Options\ListControllerRoute */
			$route = $options->$getter();
			if(!$route->getDisabled()){
				// [ ['group1', 'group2', ...], 'resource name' ]
				$rules[] = [$route->getAllowedRoles(), 'route/'.$route->getRoute()];
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
