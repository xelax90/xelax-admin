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
	 * Could make some wierd effects when set in config
	 * @var ListControllerOptions 
	 */
	protected $parentOptions = null;
	/**
	 * @var string The name of the entity attribute that contains the parent
	 */
	protected $parentAttributeName = '';

	/** 
	 * Options of child controller
	 * @var array
	 */
	protected $childOptions = array();
	
	
	/** @var string Name of the controller. Used to generate routes */
	protected $name;
	/** @var string Full class name of the controller. Used to generate routes */
	protected $controllerClass;
	/** @var string Module namespace for generating Entity and Form class */
	protected $baseNamespace;
	/** @var string Entity class. Defaults to _BaseNamespace_\Entity\_Name_ */
	protected $entityClass;
	/** @var string Form class. Defaults to _BaseNamespace_\Form\_Name_Form */
	protected $formClass;
	/** @var string Title of the Button. Only used when this is a child route */
	protected $buttonTitle;
	
	
	/** @var boolean Set to true if you want REST functionality. Only POST Requests support file uploading so far! */
	protected $restEnabled = false;
	
	/** @var string List view heading */
	protected $listTitle;
	/** @var string Edit view heading */
	protected $editTitle;
	/** @var string Create view heading */
	protected $createTitle;
	
	/** @var string The name of the route pointing to this option (defaults to the config key) */
	protected $routeBase;
	
	/** @var ListControllerRoute route to list. Leave empty to auto-generate */
	protected $listRoute;
	/** @var ListControllerRoute route to create. Leave empty to auto-generate */
	protected $createRoute;
	/** @var ListControllerRoute route to edit. Leave empty to auto-generate */
	protected $editRoute;
	/** @var ListControllerRoute route to delete. Leave empty to auto-generate */
	protected $deleteRoute;
	
	/** @var array Array of ListControllerButton to be shown next to Edit/Delete buttons in list view */
	protected $buttons;
	
	/** @var string Prompt when clicking delete */
	protected $deleteWarningText;
	/** @var string Caption of 'Add new' button */
	protected $createText;
	
	/** @var string Columns to show in list view */
	protected $listColumns;
	/** @var string number of items per page in list view */
	protected $pageLength;
	
	/** @var string Id parameter for all views and child controllers */
	protected $idParamName;
	/** @var string Alias parameter name for all view and child controllers */
	protected $aliasParamName;
	
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
		if(empty($this->listRoute)){
			$this->setListRoute(array());
		}
		return $this->listRoute;
	}

	public function getCreateRoute() {
		if(empty($this->createRoute)){
			$this->setCreateRoute(array());
		}
		return $this->createRoute;
	}

	public function getEditRoute() {
		if(empty($this->editRoute)){
			$this->setEditRoute(array());
		}
		return $this->editRoute;
	}

	public function getDeleteRoute() {
		if(empty($this->deleteRoute)){
			$this->setDeleteRoute(array());
		}
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

	public function getAliasParamName() {
		return $this->aliasParamName;
	}

	public function getIdParamName() {
		return $this->idParamName;
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
	
	public function getParentAttributeName(){
		return $this->parentAttributeName;
	}

	public function getChildOptions() {
		return $this->childOptions;
	}

	public function getRouteBase() {
		return $this->routeBase;
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
		if(!$listRoute instanceof ListControllerRoute){
			$listRoute = new ListControllerRoute($listRoute);
		}
		$this->listRoute = $listRoute;
		return $this;
	}

	public function setCreateRoute($createRoute) {
		if(!$createRoute instanceof ListControllerRoute){
			$createRoute = new ListControllerRoute($createRoute);
		}
		$this->createRoute = $createRoute;
		return $this;
	}

	public function setEditRoute($editRoute) {
		if(!$editRoute instanceof ListControllerRoute){
			$editRoute = new ListControllerRoute($editRoute);
		}
		$this->editRoute = $editRoute;
		return $this;
	}

	public function setDeleteRoute($deleteRoute) {
		if(!$deleteRoute instanceof ListControllerRoute){
			$deleteRoute = new ListControllerRoute($deleteRoute);
		}
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

	public function setAliasParamName($aliasParamName) {
		$this->aliasParamName = $aliasParamName;
		return $this;
	}

	public function setIdParamName($IdParamName) {
		$this->idParamName = $idParamName;
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
	
	public function setParentAttributeName($parentAttributeName) {
		$this->parentAttributeName = $parentAttributeName;
		return $this;
	}

	public function setChildOptions($childOptions) {
		$this->childOptions = $childOptions;
		return $this;
	}
	
	public function setRouteBase($routeBase) {
		$this->routeBase = $routeBase;
		return $this;
	}
	
	public function getBaseNamespace() {
		return $this->baseNamespace;
	}

	public function setBaseNamespace($baseNamespace) {
		$this->baseNamespace = $baseNamespace;
		return $this;
	}
	
	public function getEntityClass() {
		return $this->entityClass;
	}

	public function getFormClass() {
		return $this->formClass;
	}

	public function setEntityClass($entityClass) {
		$this->entityClass = $entityClass;
		return $this;
	}

	public function setFormClass($formClass) {
		$this->formClass = $formClass;
		return $this;
	}
	
	public function getRestEnabled() {
		return $this->restEnabled;
	}

	public function setRestEnabled($restEnabled) {
		$this->restEnabled = $restEnabled;
		return $this;
	}
	
	public function getButtonTitle() {
		return $this->buttonTitle;
	}

	public function setButtonTitle($buttonTitle) {
		$this->buttonTitle = $buttonTitle;
		return $this;
	}
	
	public function getButtons() {
		return $this->buttons;
	}

	public function setButtons($buttons) {
		foreach($buttons as $key => $button){
			if(!$button instanceof ListControllerButton){
				$buttons[$key] = new ListControllerButton($button);
			}
		}
		$this->buttons = $buttons;
		return $this;
	}

	public function __construct($options = null) {
		if(!empty($options['parent_options'])){ // this should not be used I guess..
			$parentOptions = new static($options['parent_options']);
			$options['parent_options'] = $parentOptions;
		}
		
		if(!empty($options['child_options'])){
			$childOptions = array();
			foreach($options['child_options'] as $key => $option){
				if(!empty($options['child_options']['route_base'])){
					// ignore route base on children
					$options['child_options']['route_base'] = null;
				}
				$child = new static($option);
				$child->setParentOptions($this);
				if(empty($child->getParentAttributeName())){
					$child->setParentAttributeName(lcfirst($options['name']));
				}
				$childOptions[$key] = $child;
			}
			$options['child_options'] = $childOptions;
		}
		
		parent::__construct($options);
		
		// generate missing values
		if(empty($this->listTitle)){
			$this->listTitle = gettext_noop('%ss');
		}
		
		if(empty($this->buttonTitle)){
			$this->buttonTitle = $this->name;
		}
		
		if(empty($this->editTitle)){
			$this->editTitle = gettext_noop('Edit %s');
		}
		
		if(empty($this->createTitle)){
			$this->createTitle = gettext_noop('Create %s');
		}
		
		if(empty($this->deleteWarningText)){
			$this->deleteWarningText = gettext_noop('Really delete %s?');
		}
		
		if(empty($this->createText)){
			$this->createText = gettext_noop('Add new %s');
		}
		
		if($this->pageLength === null){
			$this->pageLength = 10;
		}
		
		if(empty($this->aliasParamName)){
			$this->aliasParamName = strtolower($this->name).'_alias';
		}
		
		if(empty($this->idParamName)){
			$this->idParamName = strtolower($this->name).'_id';
		}
		
		if(empty($this->idName)){
			$this->idName = 'id';
		}
		
		if(empty($this->aliasName)){
			$this->aliasName = 'alias';
		}
		
		if(empty($this->listRoute)){
			$this->setListRoute(array());
		}
		
		if(empty($this->createRoute)){
			$this->setCreateRoute(array());
		}
		
		if(empty($this->editRoute)){
			$this->setEditRoute(array());
		}
		
		if(empty($this->deleteRoute)){
			$this->setDeleteRoute(array());
		}
		
		if(empty($this->entityClass) && !empty($this->baseNamespace)){
			$this->entityClass = $this->baseNamespace."\\Entity\\".$this->name;
		}
		
		if(empty($this->formClass) && !empty($this->baseNamespace)){
			$this->formClass = $this->baseNamespace."\\Form\\".$this->name."Form";
		}
	}
}