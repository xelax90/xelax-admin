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
					$controllerOptions[$key] = new \XelaxAdmin\Options\ListControllerOptions($options);
				}
				return $controllerOptions;
			},
			'XelaxAdmin\Provider\Rule\ListController' => function($sm){
				return new \XelaxAdmin\Provider\Rule\ListController($sm);
			},
			'XelaxAdmin\Provider\Resource\ListController' => function($sm){
				return new \XelaxAdmin\Provider\Resource\ListController($sm);
			}
		)
	),

    'xelax' => array(
		'list_controller' => array(
			'menus' => array(
				'name' => 'Menu', // this will be the route url and is used to generate texts
				'controller_class' => 'MyModule\Controller\MenuListController',
				'child_options' => array(
					'item' => array(
						'name' => 'Item',
						'controller_class' => 'MyModule\Controller\MenuItemListController',
						'list_title' => 'Menu Items', 
						// for other options, see XelaxAdmin\Options\ListControllerOptions
					),
				),
			),
		),
	),

	'bjyauthorize' => array(
        'resource_providers' => array(
			"XelaxAdmin\Provider\Resource\ListController" => null,
        ),
		
		'rule_providers' => array(
			"XelaxAdmin\Provider\Rule\ListController" => null,
		),
	),
);
