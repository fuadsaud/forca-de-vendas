<?php

namespace ApplicationTest\Form;

use ApplicationTest\Bootstrap;

class UserTest extends \PHPUnit_Framework_TestCase
{

    public function testCorrectClasses()
    {
        $form = $this->getTable()->getForm('edit');
        $this->assertInstanceOf('Application\Form\UserForm', $form);
        $this->assertInstanceOf('Application\Form\UserEditFilter', $form->getInputFilter());

        $form = $this->getTable()->getForm('create');
        $this->assertInstanceOf('Application\Form\UserForm', $form);
        $this->assertInstanceOf('Application\Form\UserFilter', $form->getInputFilter());
    }

    public function testCreate()
    {
        $form = $this->getTable()->getForm('create');
        $data = array(
            'name' => 'Luiz',
            'email' => 'felipe.silvacunha@gmail.com',
            'password' => '123',
            'confirmation' => '123'
        );
        $form->setData($data);
        $this->assertTrue($form->isValid());
    }

    public function testCreateFailure()
    {
        $form = $this->getTable()->getForm('create');
        $data = array(
            'name' => null,
            'email' => 'a',
            'password' => null,
            'confirmation' => '12'
        );
        $form->setData($data);
        $this->assertFalse($form->isValid());
        $errors = $form->getMessages();
        $this->assertArrayHasKey('name', $errors);
        $this->assertArrayHasKey('email', $errors);
        $this->assertArrayHasKey('password', $errors);
        $this->assertArrayHasKey('confirmation', $errors);
        $this->assertCount(4, $errors);
    }


    public function testEditWithFullData()
    {
        $form = $this->getTable()->getForm('edit');
        $data = array(
            'name' => 'Luiz',
            'email' => 'felipe.silvacunha@gmail.com',
            'password' => '123',
            'confirmation' => '123'
        );

        $form->setData($data);
        $this->assertTrue($form->isValid());
    }

    public function testEditWithPartialData()
    {
        $form = $this->getTable()->getForm('edit');
        $data = array('name' => 'Luiz');
        $form->setData($data);
        $this->assertTrue($form->isValid());
    }

    public function testEditWithInvalidData()
    {
        $form = $this->getTable()->getForm('edit');
        $form->setData(array());
        $this->assertFalse($form->isValid());
        $errors = $form->getMessages();
        $this->assertArrayHasKey('name', $errors);
        $this->assertArrayHasKey('email', $errors);
        $this->assertArrayHasKey('password', $errors);
        $this->assertArrayHasKey('group_id', $errors);
    }

    public function testEditInvalidEmail()
    {
        $form = $this->getTable()->getForm('edit');
        $form->setData(array('email' => 'a'));
        $this->assertFalse($form->isValid());
        $errors = $form->getMessages();
        $this->assertArrayHasKey('email', $errors);
        $this->assertCount(1, $errors);
    }

    public function testEditInvalidConfirmation()
    {
        $form = $this->getTable()->getForm('edit');
        $form->setData(array('password' => '123', 'confirmation' => '124'));
        $this->assertFalse($form->isValid());
        $errors = $form->getMessages();
        $this->assertArrayHasKey('confirmation', $errors);
        $this->assertCount(1, $errors);
    }

    protected function getTable()
    {
        return Bootstrap::getServiceManager()->get('Application\Model\UsersTable');
    }
}
