<?php

namespace ApplicationTest\Form;

use ApplicationTest\Bootstrap;

class CategoryTest extends \PHPUnit_Framework_TestCase
{

    public function testCorrectClasses()
    {
        $form = $this->getTable()->getForm('edit');
        $this->assertInstanceOf('Application\Form\CategoryForm', $form);
        $this->assertInstanceOf('Application\Form\CategoryFilter', $form->getInputFilter());

        $form = $this->getTable()->getForm('create');
        $this->assertInstanceOf('Application\Form\CategoryForm', $form);
        $this->assertInstanceOf('Application\Form\CategoryFilter', $form->getInputFilter());
    }

    public function testCreate()
    {
        $form = $this->getTable()->getForm('create');
        $data = array(
            'name' => 'cat1',
        );
        $form->setData($data);
        $this->assertTrue($form->isValid());
    }

    public function testCreateFailure()
    {
        $form = $this->getTable()->getForm('create');
        $data = array(
            'name' => null,
        );
        $form->setData($data);
        $this->assertFalse($form->isValid());
        $errors = $form->getMessages();
        $this->assertArrayHasKey('name', $errors);
        $this->assertCount(1, $errors);
    }


    public function testEdit()
    {
        $form = $this->getTable()->getForm('edit');
        $data = array(
            'name' => 'cat New',
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
    }

    protected function getTable()
    {
        return Bootstrap::getServiceManager()->get('Application\Model\CategoriesTable');
    }
}
