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
            'stock_quantity' => 10,
            'price' => 1.99
        );
    }

    protected function getUpdateData()
    {
        return array(
            'id' => 1,
            'name' => 'New Product',
            'description' => 'New Description',
            'active' => 1,
            'stock_quantity' => 11,
            'price' => 2.00,
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
        $this->setExpectedException('Application\Exception\RuntimeException');

        $serviceManager = $this->getServiceManager();
        $oldObject = $serviceManager->get('ProductsCategories');
        $mock = $this->getMock('Zend\Db\TableGateway\TableGateway', array(), array(), '', false);
        $mock->expects($this->once())->method('delete')->willThrowException(new \Exception());
        $this->getServiceManager()->setAllowOverride(true);
        $this->getServiceManager()->setService('ProductsCategories', $mock);
        $stub = $this->getMockBuilder('Application\Model\ProductsTable')
            ->setConstructorArgs(array(new \Zend\Db\TableGateway\TableGateway('products', $serviceManager->get('Zend\Db\Adapter\Adapter')), $serviceManager))
            ->setMethods(array('beginTransaction', 'rollback'))
            ->getMock();
        $stub->expects($this->once())->method('beginTransaction');
        $stub->expects($this->once())->method('rollback');
        $stub->delete(array('id' => 1));
    }

    public function testUpdatePrice()
    {
        $data = $this->getUpdateData();
        $id = $data['id'];
        unset($data['id']);

        $priceTable = $this->getServiceManager()->get('ProductsPrices');
        $currentPrice = $priceTable->select(array(
            'product_id' => $id,
            new \Zend\Db\Sql\Predicate\IsNull('final_date')
        ))->current();

        $this->getTable()->save($id, $data);

        $newPrice = $priceTable->select(array(
            'product_id' => $id,
            new \Zend\Db\Sql\Predicate\IsNull('final_date')
        ))->current();
        $this->assertNotEquals($currentPrice['id'], $newPrice['id']);
        $data = $priceTable->select(array('id' => $currentPrice['id']))->current();
        $this->assertNotNull($data['final_date']);
        $this->assertLessThanOrEqual(new \DateTime(), new \DateTime($data['final_date']));
    }

}
