<?php
namespace XelaxAdmin;

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

$xelaxConfig = array(
	/*
	 * Configure your list controllers. Routes are generated automatically, but
	 * you can also create your own routes. Route names must correspond with 
	 * controller names.
	 */
	'list_controller' => array(
		'menus' => array(
			'name' => 'Menu', // this will be the route url and is used to generate texts
			'controller_class' => 'XelaxAdmin\Controller\ListController',
			// route_base defaults to the config key ('menus' in this case). 
			'route_base' => 'menus_foo', // only available at top-level options
			'create_route' => array(
				'allowed_roles' => array('moderator'),
			),
			'edit_route' => array(
				'allowed_roles' => array('moderator'),
			),
			'delete_route' => array(
				'allowed_roles' => array('administrator'),
			),
			'child_options' => array(
				'item' => array(
					'name' => 'Item',
					'controller_class' => 'XelaxAdmin\Controller\ListController',
					'list_title' => 'Menu Items', 
					'create_route' => array(
						'allowed_roles' => array('moderator'),
					),
					'edit_route' => array(
						'allowed_roles' => array('moderator'),
					),
					'delete_route' => array(
						'allowed_roles' => array('administrator'),
					),
					// for other options, see XelaxAdmin\Options\ListControllerOptions
				),
			),
		),
	),
);

$routerConfig = array(
	/**
	 * Configure the routes pointing to the list.
	 * The route name can be chosen arbitrary, but has to be linked in 
	 * the controller options as 'route_base' if it differs from the options key
	 */
	'menus_foo' => array(
		'type' => 'XelaxAdmin\Router\ListRoute',
		'options' => array(
			// the config key of the options
			'controller_options_name' => 'menus',
		),
	),
);

$guardConfig = array(
	/**
	 * Each route needs a guard, or it cannot be accessed.
	 * The Access control inside the list controller options is an additional filter.
	 * You can make the route unrestricted and restrict access to specific actions inside the list controller
	 */
	array('route' => 'menus_foo', 'roles' => array('user', 'guest')),
);


return array(
	'service_manager' => array(
		'factories' => array(
			'XelaxAdmin\ListControllerOptions' => function($sm){
				$config = $sm->get('Config');
				$listConfig = array();
				if(isset($config['xelax']['list_controller'])){
					$listConfig = $config['xelax']['list_controller'];
				}
				$controllerOptions = array();
				foreach($listConfig as $key => $options){
					$controllerOptions[$key] = new Options\ListControllerOptions($options);
				}
				return $controllerOptions;
			},
			'XelaxAdmin\Provider\ListController' => function($sm){
				$provider = new Provider\ListController();
				$provider->setServiceLocator($sm);
				return $provider;
			},
			'XelaxAdmin\Guard\ListController' => function($sm){
				$guard = new Guard\ListController();
				$guard->setServiceLocator($sm);
				return $guard;
			},
            'Router' => 'XelaxAdmin\Service\RouterFactory',
		),
		'invokables' => array(
			'XelaxAdmin\ListControllerRouteInjecter' => 'XelaxAdmin\\Service\\ListControllerRouteInjecter',
		)
	),
	'controllers' => array(
		'invokables' => array(
			'XelaxAdmin\Controller\ListController' => 'XelaxAdmin\Controller\ListController'
		),
	),

    'xelax' => $xelaxConfig,
	'router' => array(
		'routes' => $routerConfig,
	),

	'bjyauthorize' => array(
		'guards' => array(
			'XelaxAdmin\Guard\ListController' => null,
            'BjyAuthorize\Guard\Route' => $guardConfig,
		),
		
        'resource_providers' => array(
			"XelaxAdmin\Provider\ListController" => null,
        ),
		
		'rule_providers' => array(
			"XelaxAdmin\Provider\ListController" => null,
		),
	),
);
