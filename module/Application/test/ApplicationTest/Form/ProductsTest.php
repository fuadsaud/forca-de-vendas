<?php

namespace ApplicationTest\Form;

use ApplicationTest\Bootstrap;

class ProductTest extends \PHPUnit_Framework_TestCase
{

    public function testCorrectClasses()
    {
        $form = $this->getTable()->getForm('edit');
        $this->assertInstanceOf('Application\Form\ProductForm', $form);
        $this->assertInstanceOf('Application\Form\ProductFilter', $form->getInputFilter());

        $form = $this->getTable()->getForm('create');
        $this->assertInstanceOf('Application\Form\ProductForm', $form);
        $this->assertInstanceOf('Application\Form\ProductFilter', $form->getInputFilter());
    }

    public function testCreate()
    {
        Bootstrap::getServiceManager()->get('FixturesRunner')->uses(array('categories', 'products'));
        $form = $this->getTable()->getForm('create');
        $data = array(
            'name' => 'Sapato',
            'description' => 'Sapato salto alto',
            'price' => '12.00',
            'stock_quantity' => 10,
            'categories' => array('1')
        );
        $form->setData($data);
        $this->assertTrue($form->isValid());
    }

    public function testCreateFailure()
    {
        $form = $this->getTable()->getForm('create');
        $data = array(
            'name' => null,
            'description' => null,
            'price' => null,
            'stock_quantity' => null,
            'categories' => null,
        );
        $form->setData($data);
        $this->assertFalse($form->isValid());
        $errors = $form->getMessages();
        $this->assertArrayHasKey('name', $errors);
        $this->assertArrayHasKey('description', $errors);
        $this->assertArrayHasKey('price', $errors);
        $this->assertArrayHasKey('stock_quantity', $errors);
        $this->assertArrayHasKey('categories', $errors);
        $this->assertCount(5, $errors);
    }


    public function testCreateWithInvalidCategory() {
        $form = $this->getTable()->getForm('create');
        $data = array(
            'categories' => array(999999),
        );
        $form->setData($data);
        $this->assertFalse($form->isValid());
        $errors = $form->getMessages();
        $this->assertArrayHasKey('categories', $errors);
    }

    public function testEditWithInvalidCategory() {
        $form = $this->getTable()->getForm('edit');
        $data = array(
            'categories' => array(999999),
        );
        $form->setData($data);
        $this->assertFalse($form->isValid());
        $errors = $form->getMessages();
        $this->assertArrayHasKey('categories', $errors);
    }


    public function testEdit()
    {
        Bootstrap::getServiceManager()->get('FixturesRunner')->uses(array('categories', 'products'));
        $form = $this->getTable()->getForm('edit');
        $data = array(
            'name' => 'cat New',
            'description' => 'Sapato salto alto',
            'price' => '12.00',
            'stock_quantity' => 10,
            'categories' => array('1')
        );

        $form->setData($data);
        $this->assertTrue($form->isValid());
    }

    public function testEditWithInvalidData()
    {
        $form = $this->getTable()->getForm('edit');
        $form->setData(array(
            'price' => 'a'
        ));
        $this->assertFalse($form->isValid());
        $errors = $form->getMessages();
        $this->assertArrayHasKey('name', $errors);
        $this->assertArrayHasKey('description', $errors);
        $this->assertArrayHasKey('price', $errors);
        $this->assertArrayHasKey('stock_quantity', $errors);
        $this->assertArrayHasKey('categories', $errors);
        $this->assertCount(5, $errors);
    }

    protected function getTable()
    {
        return Bootstrap::getServiceManager()->get('Application\Model\ProductsTable');
    }
}
