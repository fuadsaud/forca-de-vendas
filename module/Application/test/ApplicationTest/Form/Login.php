<?php

namespace ApplicationTest\Form;

use ApplicationTest\Bootstrap;

class LoginFormTest extends \PHPUnit_Framework_TestCase
{
    public function testLogin()
    {
        $form = Bootstrap::getServiceManager()->get('Application\Model\UsersTable')->getForm('Login');
        $this->assertInstanceOf('Application\Form\LoginForm', $form);
        $data = array(
            'username' => 'felipe.silvacunha@gmail.com',
            'password' => '123',
        );

        $this->assertTrue($form->isValid($data));
        $data = array(
            'username' => 'felipe.silvacunha@gmail.com',
        );

        $this->assertFalse($form->isValid($data));
    }
}
