<?php

namespace Application\Form;

use Zend\InputFilter\InputFilter;

class GroupFilter extends InputFilter
{

    public function __construct()
    {
        $this->add(array(
            'name' => 'name',
            'required' => true,
        ));
    }
}
