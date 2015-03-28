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

use Zend\Form\Form;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;
use Zend\InputFilter\InputFilter;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;

/**
 * Item Form
 *
 * @author schurix
 */
class ItemForm extends Form implements ObjectManagerAwareInterface{
	protected $em;
	
	public function __construct(){
		
		// we want to ignore the name passed
		parent::__construct('item');
		
		$this->setAttribute('method', 'post')
             ->setHydrator(new ClassMethodsHydrator(false))
             ->setInputFilter(new InputFilter());
		
		$this->add(array(
			'name' => 'item',
            'type' => 'MyModule\Form\ItemFieldset',
            'options' => array(
                'use_as_base_fieldset' => true,
            ),
        ));
		
		$this->add(array(
			'name' => 'submit',
			'type' => 'Submit',
			'attributes' => array(
				'value' => 'Speichern',
				'class' => 'btn-success'
			),
			'options' => array(
				'as-group' => true,
			)
		));
		
	}

	public function getObjectManager() {
		return $this->em;
	}

	public function setObjectManager(\Doctrine\Common\Persistence\ObjectManager $objectManager) {
		$this->em = $objectManager;
		
		if($this->get('item') instanceof ObjectManagerAwareInterface){
			$this->get('item')->setObjectManager($objectManager);
		}
	}
}
