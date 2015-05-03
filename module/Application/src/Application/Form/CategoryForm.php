<?php

namespace Application\Form;

use Zend\Form;

class CategoryForm extends Form\Form
{
    public function __construct($name = 'group')
    {
        parent::__construct($name);
        $this->init();
    }

    public function init()
    {
        $this->add(array(
            'name' => 'name'
        ));
    }
}
