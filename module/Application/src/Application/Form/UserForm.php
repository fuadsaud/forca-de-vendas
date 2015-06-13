<?php

namespace Application\Form;

use Zend\Form;

class UserForm extends Form\Form
{
    public function __construct($name = 'user')
    {
        parent::__construct($name);
        $this->init();
    }

    public function init()
    {
        $this->add(array(
            'name' => 'name'
        ))->add(array(
            'name' => 'id'
        ))->add(array(
            'name' => 'email'
        ))->add(array(
            'name' => 'password',
            'type' => 'password',
        ))->add(array(
            'name' => 'confirmation',
            'type' => 'password',
        ))->add(array(
            'name' => 'group_id',
        ));
    }

    public function getData($flag = Form\FormInterface::VALUES_NORMALIZED)
    {
        $data = parent::getData($flag);
        unset($data['confirmation']);

        foreach ($data as $key => $value) {
            if (empty($value)) {
                unset($data[$key]);
            }
        }

        return $data;
    }
}
