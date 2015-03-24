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

namespace XelaxAdmin\Guard;

use BjyAuthorize\Guard\GuardInterface;
use BjyAuthorize\Guard\Route as RouteGuard;
use BjyAuthorize\Exception\UnAuthorizedException;

use Zend\EventManager\EventManagerInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Mvc\MvcEvent;


/**
 * A guard for the ListController routes
 *
 * @author schurix
 */
class ListController implements GuardInterface, ServiceLocatorAwareInterface{
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();
	
	
    public function onRoute(MvcEvent $event)
    {
		$err = RouteGuard::ERROR;
		
        $service    = $this->serviceLocator->get('BjyAuthorize\Service\Authorize');
		/* @var $match \Zend\Mvc\Router\RouteMatch */
        $match      = $event->getRouteMatch();
        $routeName  = $match->getMatchedRouteName();
		$privilege  = $match->getParam('xelax_admin_privilege');
		if(empty($privilege) || $service->isAllowed('xelax-route/' . $routeName, $privilege)) {
			return;
		}

        $event->setError($err);
        $event->setParam('route', $routeName);
        $event->setParam('identity', $service->getIdentity());
        $event->setParam('exception', new UnAuthorizedException('You are not authorized to access ' . $routeName));

        /* @var $app \Zend\Mvc\Application */
        $app = $event->getTarget();

        $app->getEventManager()->trigger(MvcEvent::EVENT_DISPATCH_ERROR, $event);
    }

	public function attach(EventManagerInterface $events) {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, array($this, 'onRoute'), -1000);
	}

	public function detach(EventManagerInterface $events) {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
	}

	public function getServiceLocator() {
		return $this->serviceLocator;
	}

	public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
		$this->serviceLocator = $serviceLocator;
	}

}
