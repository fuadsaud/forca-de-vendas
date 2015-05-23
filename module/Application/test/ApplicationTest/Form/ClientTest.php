<?php

namespace ApplicationTest\Form;

use ApplicationTest\Bootstrap;

class ClientTest extends \PHPUnit_Framework_TestCase
{

    public function testCorrectClasses()
    {
        $form = $this->getTable()->getForm('edit');
        $this->assertInstanceOf('Application\Form\ClientForm', $form);
        $this->assertInstanceOf('Application\Form\ClientFilter', $form->getInputFilter());

        $form = $this->getTable()->getForm('create');
        $this->assertInstanceOf('Application\Form\ClientForm', $form);
        $this->assertInstanceOf('Application\Form\ClientFilter', $form->getInputFilter());
    }

    public function testCreate()
    {
        $form = $this->getTable()->getForm('create');
        $data = array(
            'name' => 'Acougue e Mercearia Mossmann',
            'cnpj' => '15415331000160',
            'trading_name' => 'Acougue e Mercearia Mossmann',
            'email' => 'mossmann@gmail.com'
        );
        $form->setData($data);
        $this->assertTrue($form->isValid());
    }

    public function testCreateFailure()
    {
        $form = $this->getTable()->getForm('create');
        $data = array();
        $form->setData($data);
        $this->assertFalse($form->isValid());
        $errors = $form->getMessages();
        $this->assertArrayHasKey('name', $errors);
        $this->assertArrayHasKey('cnpj', $errors);
        $this->assertArrayHasKey('trading_name', $errors);
        $this->assertArrayHasKey('email', $errors);
        $this->assertCount(4, $errors);
    }


    public function testEdit()
    {
        $form = $this->getTable()->getForm('edit');
        $data = array(
            'name' => 'Acougue e Mercearia Mossmann',
            'cnpj' => '15415331000160',
            'trading_name' => 'Acougue e Mercearia Mossmann',
            'email' => 'mossmann@gmail.com'
        );

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
        $this->assertArrayHasKey('cnpj', $errors);
        $this->assertArrayHasKey('trading_name', $errors);
        $this->assertArrayHasKey('email', $errors);
        $this->assertCount(4, $errors);
    }

    public function testInvalidEmail()
    {
        $form = $this->getTable()->getForm('create');
        $this->invalidEmailForm($form);

        $form = $this->getTable()->getForm('edit');
        $this->invalidEmailForm($form);
    }

    private function invalidEmailForm($form)
    {
        $form->setData(array('email' => 'fsahfhbsfefefwe.com'));
        $this->assertFalse($form->isValid());
        $errors = $form->getMessages();
        $this->assertArrayHasKey('email', $errors);
    }

    public function testInvalidCnpj()
    {
        $form = $this->getTable()->getForm('create');
        $this->invalidCnpjForm($form);

        $form = $this->getTable()->getForm('edit');
        $this->invalidCnpjForm($form);
    }

    private function invalidCnpjForm($form)
    {
        $form->setData(array('cnpj' => '9999999999999999'));
        $this->assertFalse($form->isValid());
        $errors = $form->getMessages();
        $this->assertArrayHasKey('cnpj', $errors);
    }

    protected function getTable()
    {
        return Bootstrap::getServiceManager()->get('Application\Model\ClientsTable');
    }
}
