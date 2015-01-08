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

namespace XelaxAdmin\Options;

use Zend\Stdlib\AbstractOptions;
 
class ListControllerOptions extends AbstractOptions
{
	/** 
	 * Options of parent controller
	 * @var ListControllerOptions 
	 */
	protected $parentOptions = null;

	/** 
	 * Options of child controller
	 * @var array() 
	 */
	protected $childOptions = array();
	
	
	/** @var string Name of the controller. Used to generate routes */
	protected $name;
	/** @var string Full class name of the controller. Used to generate routes */
	protected $controllerClass;
	
	/** @var string List view heading */
	protected $listTitle;
	/** @var string Edit view heading */
	protected $editTitle;
	/** @var string Create view heading */
	protected $createTitle;
	
	/** @var string route to list */
	protected $listRoute;
	/** @var string route to create */
	protected $createRoute;
	/** @var string route to edit */
	protected $editRoute;
	/** @var string route to delete */
	protected $deleteRoute;
	
	/** @var string Prompt when clicking delete */
	protected $deleteWarningText;
	/** @var string Caption of 'Add new' button */
	protected $createText;
	
	/** @var string Columns to show in list view */
	protected $listColumns;
	/** @var string number of items per page in list view */
	protected $pageLength;
	
	/** @var string Id parameter name in delete route */
	protected $deleteParamName;
	/** @var string Id parameter name in edit route */
	protected $editParamName;
	/** @var string Alias parameter name for all view and child controllers */
	protected $aliasParamName;
	/** @var string Id parameter name for child controllers */
	protected $childRouteParamName;
	
	/** @var string name of id parameter. Function name 'get'.ucfirst($idName) will be used to get the id */
	protected $idName;
	/** @var string name of alias parameter. Function name 'get'.ucfirst($aliasName) will be used to get the alias */
	protected $aliasName;
	
	public function getName() {
		return $this->name;
	}
	
	public function getControllerClass() {
		return $this->controllerClass;
	}

	public function getListTitle() {
		return $this->listTitle;
	}

	public function getEditTitle() {
		return $this->editTitle;
	}

	public function getCreateTitle() {
		return $this->createTitle;
	}

	public function getListRoute() {
		return $this->listRoute;
	}

	public function getCreateRoute() {
		return $this->createRoute;
	}

	public function getEditRoute() {
		return $this->editRoute;
	}

	public function getDeleteRoute() {
		return $this->deleteRoute;
	}

	public function getDeleteWarningText() {
		return $this->deleteWarningText;
	}

	public function getCreateText() {
		return $this->createText;
	}

	public function getListColumns() {
		return $this->listColumns;
	}

	public function getPageLength() {
		return $this->pageLength;
	}

	public function getDeleteParamName() {
		return $this->deleteParamName;
	}

	public function getEditParamName() {
		return $this->editParamName;
	}

	public function getAliasParamName() {
		return $this->aliasParamName;
	}

	public function getChildRouteParamName() {
		return $this->childRouteParamName;
	}

	public function getIdName() {
		return $this->idName;
	}

	public function getAliasName() {
		return $this->aliasName;
	}

	public function getParentOptions() {
		return $this->parentOptions;
	}

	public function getChildOptions() {
		return $this->childOptions;
	}

	public function setName($name) {
		$this->name = $name;
		return $this;
	}
	
	public function setControllerClass($controllerClass) {
		$this->controllerClass = $controllerClass;
		return $this;
	}
	
	public function setListTitle($listTitle) {
		$this->listTitle = $listTitle;
		return $this;
	}

	public function setEditTitle($editTitle) {
		$this->editTitle = $editTitle;
		return $this;
	}

	public function setCreateTitle($createTitle) {
		$this->createTitle = $createTitle;
		return $this;
	}

	public function setListRoute($listRoute) {
		$this->listRoute = $listRoute;
		return $this;
	}

	public function setCreateRoute($createRoute) {
		$this->createRoute = $createRoute;
		return $this;
	}

	public function setEditRoute($editRoute) {
		$this->editRoute = $editRoute;
		return $this;
	}

	public function setDeleteRoute($deleteRoute) {
		$this->deleteRoute = $deleteRoute;
		return $this;
	}

	public function setDeleteWarningText($deleteWarningText) {
		$this->deleteWarningText = $deleteWarningText;
		return $this;
	}

	public function setCreateText($createText) {
		$this->createText = $createText;
		return $this;
	}

	public function setListColumns($listColumns) {
		$this->listColumns = $listColumns;
		return $this;
	}

	public function setPageLength($pageLength) {
		$this->pageLength = $pageLength;
		return $this;
	}

	public function setDeleteParamName($deleteParamName) {
		$this->deleteParamName = $deleteParamName;
		return $this;
	}

	public function setEditParamName($editParamName) {
		$this->editParamName = $editParamName;
		return $this;
	}

	public function setAliasParamName($aliasParamName) {
		$this->aliasParamName = $aliasParamName;
		return $this;
	}

	public function setChildRouteParamName($childRouteParamName) {
		$this->childRouteParamName = $childRouteParamName;
		return $this;
	}

	public function setIdName($idName) {
		$this->idName = $idName;
		return $this;
	}

	public function setAliasName($aliasName) {
		$this->aliasName = $aliasName;
		return $this;
	}
	
	public function setParentOptions(ListControllerOptions $parentOptions) {
		$this->parentOptions = $parentOptions;
		return $this;
	}

	public function setChildOptions($childOptions) {
		$this->childOptions = $childOptions;
		return $this;
	}
	
	public function __construct($options = null) {
		if(!empty($options['parent_options'])){
			$parentOptions = new self($options['parent_options']);
			$options['parent_options'] = $parentOptions;
		}
		
		if(!empty($options['child_options'])){
			$childOptions = array();
			foreach($options['child_options'] as $key => $option){
				$child = new self($option);
				$childOptions[$key] = $child;
			}
			$options['child_options'] = $childOptions;
		}
		
		parent::__construct($options);
		
		// generate missing values
		
		if(empty($this->listTitle)){
			$this->listTitle = $this->name . 's';
		}
		
		if(empty($this->editTitle)){
			$this->editTitle = 'Edit ' . $this->name;
		}
		
		if(empty($this->createTitle)){
			$this->createTitle = 'Create ' . $this->name;
		}
		
		if(empty($this->deleteWarningText)){
			$this->deleteWarningText = 'Really delete '.$this->name.'?';
		}
		
		if(empty($this->createText)){
			$this->createText = 'Add new '.$this->name;
		}
		
		if(empty($this->pageLength)){
			$this->pageLength = 10;
		}
		
		if(empty($this->deleteParamName)){
			$this->deleteParamName = 'id';
		}
		
		if(empty($this->editParamName)){
			$this->editParamName = 'id';
		}
		
		if(empty($this->aliasParamName)){
			$this->aliasParamName = 'alias';
		}
		
		if(empty($this->childRouteParamName)){
			$this->childRouteParamName = strtolower($this->name).'_id';
		}
		
		if(empty($this->idName)){
			$this->idName = 'id';
		}
		
		if(empty($this->aliasName)){
			$this->aliasName = 'alias';
		}
	}
}