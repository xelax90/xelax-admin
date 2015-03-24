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

namespace XelaxAdmin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use XelaxAdmin\Options\ListControllerOptions;
use Doctrine\ORM\EntityManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;

class ListController extends AbstractActionController{
	
	/** @var \Doctrine\ORM\EntityManager */
	protected $em;
	
	/** @var \XelaxAdmin\Options\ListControllerOptions */
	protected $options = null;
	
	/** @var string */
	protected $privilegeBase = null;
	
	/**
	 * @param EntityManager $em
	 */
	public function setEntityManager(EntityManager $em){
		$this->em = $em;
	}
	
	/**
	 * @return EntityManager
	 */
	public function getEntityManager(){
		if (null === $this->em) {
			$this->em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
		}
		return $this->em;
	}
	
	/**
	 * Returns active Option name. Used to auto-generate options and entity names
	 * @return string
	 * @throws \Exception
	 */
	public function getName(){
		$name = $this->getOptions()->getName();
		if(empty($name)){
			$parts = explode("\\", get_class($this));
			$name = array_pop($parts);
			
			$name = preg_replace('/ListController$/', '', $name);
			$name = preg_replace('/Controller$/', '', $name);
			
			if (empty($name)) {
				throw new \Exception('Empty name');
			}
		}
		return $name;
	}
	
	/**
	 * Returns the child controller's options. Retuns null if no child controller
	 * @return ListControllerOptions
	 */
	public function getChildControllerOptions(){
		return $this->getOptions()->getChildOptions();
	}
	
	/**
	 * Returns the parent controller's options. Retuns null if no parent controller
	 * @return ListControllerOptions
	 */
	public function getParentControllerOptions(){
		return $this->getOptions()->getParentOptions();
	}
	
	function getPrivilegeBase() {
		if(null === $this->privilegeBase){
			$routeMatch = $this->getEvent()->getRouteMatch();
			$privilege = $routeMatch->getParam('xelax_admin_privilege');
			if(empty($privilege)){
				throw new Exception('XelaxAdmin\Router\ListRoute route required');
			}
			$parts = explode('/', $privilege);
			$action = array_pop($parts);
			$this->privilegeBase = implode('/', $parts);
		}
		return $this->privilegeBase;
	}
	
	/**
	 * Gets active controllerOptions depending on the RouteMatch
	 * @return ListControllerOptions|null
	 * @throws \Exception
	 */
	public function getOptions(){
		if(null === $this->options){
			$routeMatch = $this->getEvent()->getRouteMatch();
			$privilege = $routeMatch->getParam('xelax_admin_privilege');
			if(empty($privilege)){
				throw new Exception('XelaxAdmin\Router\ListRoute route required');
			}
			
			$options = $this->getServiceLocator()->get('XelaxAdmin\ListControllerOptions');
			$activeOption = null;
			$parts = explode('/', $privilege);
			$action = array_pop($parts);
			foreach ($parts as $part){
				if(array_key_exists($part, $options)){
					$activeOption = $options[$part];
					$options = $activeOption->getChildOptions();
				} else {
					throw new Exception("Active ListControllerOptions not found");
				}
			}
			
			if(empty($activeOption)){
				throw new Exception("Active ListControllerOptions not found");
			}
			
			$this->options = $activeOption;
		}
		return $this->options;
	}
	
	/**
	 * Returns module namespace of active controller for generating other namespaces
	 * TODO: this should be configurable in ListControllerOptions
	 * 
	 * @return string
	 * @throws \Exception
	 */
	private function getBaseNamespace(){
		$options = $this->getOptions();
		if(empty($options->getBaseNamespace())){
			$parts = explode("\\", get_class($this));
			if(count($parts) <= 1){
				throw new \Exception('Empty entity namespace');
			}
			return $parts[0];
		}
		return $options->getBaseNamespace();
	}
	
	/**
	 * Returns namespace for entities
	 * @return string
	 */
	private function getEntityNamespace(){
		return $this->getBaseNamespace()."\\Entity";
	}
	
	/**
	 * Returns namespace for forms
	 * @return string
	 */
	private function getFormNamespace(){
		return $this->getBaseNamespace()."\\Form";
	}
	
	/**
	 * Returns list of all items to show in list view. Overwrite to add custom filters
	 * @return \Traversable
	 */
	protected function getAll(){
		$em = $this->getEntityManager();
		$namespace = $this->getEntityNamespace();
		$name = $this->getName();
		$items = $em->getRepository($namespace."\\".$name)->findAll();
		return $items;
	}
	
	/**
	 * @param int|null $id
	 * @return Object
	 */
	protected function getItem($id = null){
		$em = $this->getEntityManager();
		$namespace = $this->getEntityNamespace();
		$name = $this->getName();
		$fullName = $namespace . "\\" . $name;
		if ($id) {
			$item = $em->getRepository($fullName)->find((int) $id);
		} else {
			$item = new $fullName();
		}
		return $item;
	}
	
	/**
	 * Returns form used for both edit and create. Overwrite getEditForm or 
	 * getCreateForm to use different forms for these views
	 * @param string $name
	 * @return \Zend\Form\Form
	 */
	protected function getForm($name = null){
		if (!$name) {
			$name = $this->getName();
		}
		$em = $this->getEntityManager();
		$namespace = $this->getFormNamespace();
		$frmCls = $namespace."\\".$name."Form";
		$form = new $frmCls($em);
        $form->setHydrator(new DoctrineObject($em));
		return $form;
	}
	
	/**
	 * Returns form used to edit items
	 * @return \Zend\Form\Form
	 */
	protected function getEditForm(){
		return $this->getForm();
	}
	
	/**
	 * Returns form used to create items
	 * @return \Zend\Form\Form
	 */
	protected function getCreateForm(){
		return $this->getForm();
	}
	
	public function indexAction(){
		return $this->_redirectToList();
	}
	
	public function _redirectToList(){
		return $this->redirect()->toRoute($this->getOptions()->getRouteBase(), $this->buildRouteParams());
	}
	
	public function buildRouteParams($action = 'list'){
		$params = array();
		$options = $this->getOptions();
		$match = $this->getEvent()->getRouteMatch();
		while(!empty($options)){
			$id = $match->getParam($options->getIdParamName(), "");
			if(!empty($id)){
				$params[$options->getIdParamName()] = $id;
			}
			
			$alias = $match->getParam($options->getAliasParamName(), "");
			if(!empty($alias)){
				$params[$options->getAliasParamName()] = $alias;
			}
			
			$options = $options->getParentOptions();
		}
		$params['route'] = $this->getPrivilegeBase()."/".$action;
		return $params;
	}
	
	protected function isAllowed($privilege){
		$routeName  = $this->getEvent()->getRouteMatch()->getMatchedRouteName();
		
		$service    = $this->serviceLocator->get('BjyAuthorize\Service\Authorize');
		return $service->isAllowed('xelax-route/' . $routeName, $privilege);
	}
	
	public function buildRoute($action = 'list', $checkACL = true){
		$params = $this->buildRouteParams($action);
		$routeName  = $this->getEvent()->getRouteMatch()->getMatchedRouteName();
		
		if($checkACL && !$this->isAllowed($params['route'])){
			return false;
		}
		
		return $this->url()->fromRoute($routeName, $params);
	}
	
	public function buildChildRoute($child, $id, $alias = '', $action = 'list', $checkACL = true){
		$options = $this->getOptions();
		$routeName  = $this->getEvent()->getRouteMatch()->getMatchedRouteName();
		
		if(empty($this->getChildControllerOptions()[$child])){
			return false;
		}
		
		$params = $this->buildRouteParams($action);
		$params[$options->getIdParamName()] = $id;
		$params[$options->getAliasParamName()] = $alias;
		
		$params['route'] = $this->getPrivilegeBase()."/".$child."/".$action;
		
		if($checkACL && !$this->isAllowed($params['route'])){
			return false;
		}
		
		return $this->url()->fromRoute($routeName, $params);
	}
	
	public function buildParentRoute($action = 'list', $checkACL = true){
		$params = $this->buildRouteParams($action);
		$routeName  = $this->getEvent()->getRouteMatch()->getMatchedRouteName();
		
		$privilegeParts = explode("/", $this->getPrivilegeBase());
		array_pop($privilegeParts);
		if(empty($privilegeParts)){
			return false;
		}
		array_push($privilegeParts, $action);
		$params['route'] = implode('/', $privilegeParts);
		
		if($checkACL && !$this->isAllowed($params['route'])){
			return false;
		}
		
		return $this->url()->fromRoute($routeName, $params);
	}
	
	public function listAction(){
		$items = $this->getAll();
		
		$params = array(
			'title' => $this->getOptions()->getListTitle(),
			'route_builder' => array($this, 'buildRoute'),
			'delete_warning_text' => $this->getOptions()->getDeleteWarningText(),
			'create_text' => $this->getOptions()->getCreateText(),
			'columns' => $this->getOptions()->getListColumns(),
			'rows' => $items,
			'page_length' => $this->getOptions()->getPageLength(),
			'alias_name' => $this->getOptions()->getAliasName(),
			'id_name' => $this->getOptions()->getIdName(),
		);
		
		if(!empty($this->getChildControllerOptions())){
			$params['sublist_names'] = array_keys($this->getChildControllerOptions());
			$params['sublist_route_builder'] = array($this, 'buildChildRoute');
		}
		
		if(!empty($this->getParentControllerOptions())){
			$params['parent_route_builder'] = array($this, 'buildParentRoute');
		}
		
		return $this->_showList($params);
	}
	
	protected function _showList($params){
		$page = $this->getEvent()->getRouteMatch()->getParam('p');
		$params['page'] = $page;
		
		$view = new ViewModel($params);
		$view->setTemplate('partial/admin_list.phtml');
		return $view;
	}
	
	public function createAction(){
		$form = $this->getCreateForm();
        $request = $this->getRequest();
		
        /** @var $request \Zend\Http\Request */
        if ($request->isPost()) {
			$item = $this->getItem();
			if($this->_createItem($item, $form)){
				$this->flashMessenger()->addSuccessMessage('The '.$this->getName().' was created');
				return $this->_redirectToList();
			}
        }
		$params = array(
			'title' => $this->getOptions()->getCreateTitle(),
			'route_builder' => array($this, 'buildRoute'),
			'form' => $form,
		);
		return $this->_showCreateForm($params);
	}
	
	/**
	 * This function is called before persist happens
	 * @param Object $item
	 * @return volid
	 */
	protected function _preCreate($item){
		return;
	}
	
	/**
	 * This function is called after flush happens
	 * @param Object $item
	 * @return volid
	 */
	protected function _postCreate($item){
		return;
	}
	
	protected function _createItem($item, $form){
		$em = $this->getEntityManager();
        $request = $this->getRequest();
		
        $form->bind($item);
        $form->setData($request->getPost());
        if ($form->isValid()) {
			$this->_preCreate($item);
			$em->persist($item);
			$em->flush();
			$this->_postCreate($item);
			return true;
        }
		return false;
	}
	
	protected function _showCreateForm($params){
		$view = new ViewModel($params);
		$view->setTemplate('partial/admin_create.phtml');
		return $view;
	}
		
    public function editAction(){
		$id = $this->getEvent()->getRouteMatch()->getParam($this->getOptions()->getIdParamName());
        $item = $this->getItem($id);
		
        $form = $this->getEditForm();
		
		if($this->_editItem($item, $form)){
            $this->flashMessenger()->addSuccessMessage('The '.$this->getName().' was edited');
            return $this->_redirectToList();
		}

		$params = array(
			'title' => $this->getOptions()->getEditTitle(),
			'route_builder' => array($this, 'buildRoute'),
			'delete_warning_text' => $this->getOptions()->getDeleteWarningText(),
            'form' => $form,
            'id' => $id
		);
		return $this->_showEditForm($params);
    }
	
	/**
	 * This function is called after form vaildation and bind, but before flush
	 * @param Object $item
	 * @return void
	 */
	protected function _preUpdate($item){
		return;
	}

	/**
	 * This function is called after flush
	 * @param Object $item
	 * @return void
	 */
	protected function _postUpdate($item){
		return;
	}
	
	protected function _editItem($item, $form){
		$em = $this->getEntityManager();
		$form->setBindOnValidate(false);
		$form->bind($item);
		
        /** @var $request \Zend\Http\Request */
        $request = $this->getRequest();
		if ($request->isPost()) {
			$form->setData($request->getPost());
			if ($form->isValid()) {
				$form->bindValues();
				$this->_preUpdate($item);
				$em->flush();
				$this->_postUpdate($item);
				return true;
			}
        }
		return false;
	}
	
	protected function _showEditForm($params){
		$view = new ViewModel($params);
		$view->setTemplate('partial/admin_edit.phtml');
		return $view;
	}

	public function deleteAction(){
		$id = $this->getEvent()->getRouteMatch()->getParam($this->getOptions()->getIdParamName());

		if (!$id) {
			return $this->_redirectToList();
		}

		$item = $this->getItem($id);

		if($this->_delteItem($item)){
			$this->flashMessenger()->addSuccessMessage('The '.$this->getName().' was deleted');
		} elseif($item){
			$this->flashMessenger()->addWarningMessage('The '.$this->getName().' was not deleted');
		}
		return $this->_redirectToList();
	}
	
	/**
	 * This function is called before delte happens. You may return false to cancel deleting
	 * @param Object $item
	 * @return boolean
	 */
	public function _preDelete($item){
		return true;
	}
	
	/**
	 * This function is called after delte happens.
	 * @param Object $item
	 * @return void
	 */
	public function _postDelete($item){
		return;
	}
	
	protected function _delteItem($item){
		$em = $this->getEntityManager();
		if($item){
			if($this->_preDelete($item)){
				$em->remove($item);
				$em->flush();
				$this->_postDelete($item);
				return true;
			}
		}
		return false;
	}
}