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

namespace XelaxAdmin\Options;

/**
 * Description of ListControllerRoute
 *
 * @author xelax90
 */
class ListControllerRoute {
	protected $route;
	protected $allowedRoles = ['user', 'guest'];
	protected $disabled = false;
	// TODO add id, alias and other route parameter names?
	
	public function __construct($options = array()) {
		if (isset($options['route'])) {
			$this->route = $options['route'];
		}
		if (isset($options['allowed_roles'])) {
			$this->allowedRoles = $options['allowed_roles'];
		}
		if (isset($options['disabled'])) {
			$this->disabled = $options['disabled'];
		}
	}
	
	public function getRoute() {
		return $this->route;
	}

	public function getAllowedRoles() {
		return $this->allowedRoles;
	}

	public function getDisabled() {
		return $this->disabled;
	}

	public function setRoute($route) {
		$this->route = $route;
		return $this;
	}

	public function setAllowedRoles($allowedRoles) {
		$this->allowedRoles = $allowedRoles;
		return $this;
	}

	public function setDisabled($disabled) {
		$this->disabled = $disabled;
		return $this;
	}
}
