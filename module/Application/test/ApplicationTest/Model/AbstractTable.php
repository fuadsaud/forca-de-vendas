<?php

namespace ApplicationTest\Model;

use ApplicationTest\Bootstrap;
use ApplicationTest\Assert\ArrayCompareTrait;

abstract class AbstractTable extends \PHPUnit_Framework_TestCase
{

    use ArrayCompareTrait;

    protected abstract function uses();

    protected abstract function getTable();

    protected abstract function getCreateData();

    protected abstract function getUpdateData();

    protected abstract function getDeletableId();

    protected abstract function getUndeletableId();

    public function setUp()
    {
        Bootstrap::init();
        $this->getServiceManager()->get('FixturesRunner')->uses($this->uses());
        parent::setUp();
    }

    public function getServiceManager()
    {
        return Bootstrap::getServiceManager();
    }

    public function testFind()
    {
        $result = $this->getTable()->find(1);
        $this->assertEquals(1, $result['id']);

    }

    public function testUnknowEntry()
    {
        $this->setExpectedException('Application\Exception\UnknowRegistryException');
        $this->getTable()->find(999999);
    }

    public function testFetchAll()
    {
        $result = $this->getTable()->fetchAll();
        $this->assertInstanceOf('Zend\Db\ResultSet\ResultSet', $result);
        $this->assertNotEmpty($result->toArray());
    }

    public function testPagination()
    {
        $result = $this->getTable()->fetchAll(null, array('paginated' => true));
        $this->assertInstanceOf('Zend\Paginator\Paginator', $result);
        $this->assertNotEmpty($result->getCurrentItems());
    }

    public function testCreate()
    {
        $table = $this->getTable();
        $data = $this->getCreateData();
        $result = $table->save(null, $data);
        $this->assertTrue(is_numeric($result));
        $entry = $table->find($result);
        $this->assertNotEmpty($entry);
        $this->assertArrayInto($data, $entry->getArrayCopy());
    }

    public function testCreateWithNoData()
    {
        $table = $this->getTable();
        $this->setExpectedException('Application\Exception\RuntimeException');
        $table->save(null, array());
    }

    public function testUpdate()
    {
        $table = $this->getTable();
        $data = $this->getUpdateData();
        $id = $data['id'];
        unset($data['id']);

        $result = $table->save($id, $data);
        $this->assertEquals(1, $result);
        $entry = $table->find($result);
        $this->assertNotEmpty($entry);

        $this->assertArrayInto($data, $entry->getArrayCopy());
    }

    public function testUpdateWithNoData()
    {
        $this->setExpectedException('Application\Exception\RuntimeException');
        $data = $this->getUpdateData();
        $id = $data['id'];

        $this->getTable()->save($id, array());
    }

    public function testUpdateUnkowEntry()
    {
        $data = $this->getUpdateData();
        $this->setExpectedException('Application\Exception\UnknowRegistryException');
        $this->getTable()->save(999999, $data);
    }

    public function testDelete()
    {
        $table = $this->getTable();
        $count = $this->countEntries();
        $result = $table->delete(array('id' => $this->getDeletableId()));
        $this->assertTrue($result);
        $this->assertEquals($count -1, $this->countEntries());
    }

    public function testDeleteFail()
    {
        $table = $this->getTable();
        $count = $this->countEntries();
        $result = $table->delete(array('id' => $this->getUndeletableId()));
        $this->assertFalse($result);
        $this->assertEquals($count, $this->countEntries());
    }

    protected function countEntries()
    {
        $table = $this->getTable();
        $gateway = $table->getTable();
        $select = $gateway->getSql()->select();
        $select->columns(array('count' => new \Zend\Db\Sql\Expression('COUNT(*)')));
        return $gateway->selectWith($select)->current()['count'];
    }
}
