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

use Zend\Stdlib\AbstractOptions;

/**
 * Description of ListControllerButton
 *
 * @author schurix
 */
class ListControllerButton extends AbstractOptions {
	/**
	 * Title of button
	 * @var string
	 */
	protected $title;
	
	/**
	 * Function that accepts 3 parameters: view (current view), id, alias. Can 
	 * return false to prevent rendering of the button
	 * @var callable
	 */
	protected $routeBuilder;
	
	public function getTitle() {
		return $this->title;
	}

	public function getRouteBuilder() {
		return $this->routeBuilder;
	}

	public function setTitle($title) {
		$this->title = $title;
		return $this;
	}

	public function setRouteBuilder(callable $routeBuilder) {
		$this->routeBuilder = $routeBuilder;
		return $this;
	}
}
