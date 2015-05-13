<?php

namespace ApplicationTest\Model;

use ApplicationTest\Bootstrap;

class CategoriesTableTest extends AbstractTable
{

    protected function uses()
    {
        return array('categories', 'products');
    }

    protected function getTable()
    {
        return $this->getServiceManager()->get('Application\Model\CategoriesTable');
    }

    protected function getCreateData()
    {
        return array(
            'name' => 'cat',
        );
    }

    protected function getUpdateData()
    {
        return array(
            'id' => 1,
            'name' => 'cat up',
        );
    }

    protected function getDeletableId()
    {
        return 2;
    }

    protected function getUndeletableId()
    {
        return 1;
    }


    public function testGetForms()
    {
        $table = Bootstrap::getServiceManager()->get('Application\Model\CategoriesTable');
        $this->assertInstanceOf('Zend\Form\Form', $table->getForm('create'));
        $this->assertInstanceOf('Zend\Form\Form', $table->getForm('edit'));
    }

}
