<?php
namespace XelaxAdmin;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;

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
		),
	),
	'controllers' => array(
		'invokables' => array(
			'XelaxAdmin\Controller\ListController' => 'XelaxAdmin\Controller\ListController'
		),
	),
	'form_elements' => array(
		'initializers' => array(
			'ObjectManagerInitializer' => function ($element, $formElements) {
				if ($element instanceof ObjectManagerAwareInterface) {
					$services      = $formElements->getServiceLocator();
					$entityManager = $services->get('Doctrine\ORM\EntityManager');
					$element->setObjectManager($entityManager);
				}
			},
		),
	),
	
	'bjyauthorize' => array(
		'guards' => array(
			'XelaxAdmin\Guard\ListController' => null,
		),
		
        'resource_providers' => array(
			"XelaxAdmin\Provider\ListController" => null,
        ),
		
		'rule_providers' => array(
			"XelaxAdmin\Provider\ListController" => null,
		),
	),
	
    'translator' => array(
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
	
	// view options
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
           'ViewJsonStrategy',
        ),
	),
);
