<?php

namespace ApplicationTest\Model;

class ProductsTableTest extends AbstractTable
{
    protected function getTable()
    {
        return $this->getServiceManager()->get('Application\Model\ProductsTable');
    }

    protected function uses()
    {
        return array('categories', 'products');
    }

    protected function getCreateData()
    {
        return array(
            'name' => 'New Product',
            'description' => 'New Description',
            'active' => 1,
            'stock_quantity' => 10
        );
    }

    protected function getUpdateData()
    {
        return array(
            'id' => 1,
            'name' => 'New Product',
            'description' => 'New Description',
            'active' => 1,
            'stock_quantity' => 10
        );
    }

    protected function getDeletableId()
    {
        return 1;
    }

    protected function getUndeletableId()
    {
        return 2;
    }

    public function testRollbackDelete()
    {
        $this->setExpectedException('Application\Exception\UnknowRegistryException');
        $oldObject = $this->getServiceManager()->get('ProductsCategories');
        $mock = $this->getMock('Zend\Db\TableGateway\TableGateway', array(), array(), '', false);
        $mock->expects($this->once())->method('delete')->willThrowException(new \Exception());
        $this->getServiceManager()->setAllowOverride(true);
        $this->getServiceManager()->setService('ProductsCategories', $mock);
        $this->getTable()->delete(array('id' => 1));
    }

    /*public function testGetForms()
    {
        $table = $this->getTable();
        $this->assertInstanceOf('Zend\Form\Form', $table->getForm('create'));
        $this->assertInstanceOf('Zend\Form\Form', $table->getForm('edit'));
    }*/

}
