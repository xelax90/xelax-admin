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

namespace MyModule\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;
use MyModule\Entity\Menu;

/**
 * Menu Fieldset
 *
 * @author schurix
 */
class MenuFieldset extends Fieldset implements InputFilterProviderInterface{
	public function __construct($name = "", $options = array()){
		if($name == ""){
			$name = 'menu';
		}
		parent::__construct($name, $options);
		
		$this->setHydrator(new ClassMethodsHydrator(false))
				->setObject(new Menu());
		
		$this->addFields();
	}
	
	protected function addFields(){
		$this->add(array(
			'name' => 'id',
			'type' => 'Hidden',
		));
		
		$this->add(array(
			'name' => 'title',
			'type' => 'Text',
			'options' => array(
				'label' => 'Titel',
				'column-size' => 'sm-10',
				'label_attributes' => array(
					'class' => 'col-sm-2',
				),
			),
			'attributes' => array(
				'id' => "",
			)
		));
	}

	public function getInputFilterSpecification() {
		$filters = array(
			'id' => array(
				'required' => false,
				'filters' => array(
					array('name' => 'Int'),
				),
			),
			'title' => array(
				'required' => true,
				'filters' => array(
					array('name' => 'StringTrim'),
					array('name' => 'StripTags'),
					// see xelax90/zf2-filter-htmlpurifier
					//array('name' => 'XelaxHTMLPurifier\Filter\HTMLPurifier'),
				),
			),
		);
		return $filters;
	}

}