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

namespace MyModule\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Menu Entity
 * @author schurix
 *
 * @ORM\Entity
 * @ORM\Table(name="item")
 * @property int $id
 * @property string $title
 */
class Item {
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer");
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	
	/**
	 * @ORM\Column(type="string");
	 */
	protected $title;
	
	/**
	 * @var Menu
	 * @ORM\ManyToOne(targetEntity="Menu", inversedBy="items")
	 * @ORM\JoinColumn(name="menu_id", referencedColumnName="id")
	 */
	protected $menu;
	
	public function getId() {
		return $this->id;
	}

	public function getTitle() {
		return $this->title;
	}

	public function getMenu() {
		return $this->menu;
	}

	public function setId($id) {
		$this->id = $id;
		return $this;
	}

	public function setTitle($title) {
		$this->title = $title;
		return $this;
	}

	public function setMenu(Menu $menu) {
		$this->menu = $menu;
		return $this;
	}

}

