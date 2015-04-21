<?php

namespace Application\Model;

use Application\Form;

class UsersTable extends AbstractTable
{

    protected function loadForm($identifier)
    {
        $form = null;
        switch ($identifier) {
            case 'login':
                $form = new Form\LoginForm();
                break;
            case 'create':
                $form = new Form\UserForm();
                $form->setInputFilter(new Form\UserFilter());
                break;
            case 'edit':
                $form = new Form\UserForm();
                $form->setInputFilter(new Form\UserEditFilter());
                break;
        }
        return $form;
    }

    public function save($id, $data)
    {
        if (isset($data['password'])) {
            $data['password'] = sha1($data['password']);
        }

        return parent::save($id, $data);
    }
}
