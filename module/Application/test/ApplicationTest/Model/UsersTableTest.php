<?php

namespace ApplicationTest\Model;

use ApplicationTest\Bootstrap;

class UsersTableTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Bootstrap::getServiceManager()->get('FixturesRunner')->uses(array('groups', 'users'));
        parent::setUp();
    }

    public function testFind()
    {
        $table = Bootstrap::getServiceManager()->get('Application\Model\UsersTable');
        $result = $table->find(1);
        $this->assertEquals(1, $result['id']);

        $this->setExpectedException('Application\Exception\UnknowRegistryException');
        $table->find(999999);
    }

    public function testFetchAll()
    {
        $table = Bootstrap::getServiceManager()->get('Application\Model\UsersTable');
        $result = $table->fetchAll();
        $this->assertInstanceOf('Zend\Db\ResultSet\ResultSet', $result);
        $this->assertNotEmpty($result->toArray());

        $result = $table->fetchAll(null, array('paginated' => true));
        $this->assertInstanceOf('Zend\Paginator\Paginator', $result);
        $this->assertNotEmpty($result->getCurrentItems());
    }

    public function testCreate()
    {
        $table = Bootstrap::getServiceManager()->get('Application\Model\UsersTable');
        $data = array(
            'name' => 'Luiz',
            'email' => 'felipe.silvacunha@gmail.com',
            'password' => '123',
            'group_id' => 1,
        );

        $result = $table->save(null, $data);
        $this->assertTrue(is_numeric($result));
        $user = $table->find($result);
        $this->assertNotEmpty($user);
        $this->assertEquals(sha1(123), $user['password']);

        $this->setExpectedException('Application\Exception\RuntimeException');
        $table->save(null, array());
    }

    public function testUpdate()
    {
        $table = Bootstrap::getServiceManager()->get('Application\Model\UsersTable');
        $data = array(
            'name' => 'Juca',
        );

        $result = $table->save(1, $data);
        $this->assertEquals(1, $result);
        $user = $table->find($result);
        $this->assertNotEmpty($user);
        $this->assertEquals('Juca', $user['name']);

        $this->setExpectedException('Application\Exception\UnknowRegistryException');
        $table->save(999999, $data);
    }

    public function testUpdateException()
    {
        $table = Bootstrap::getServiceManager()->get('Application\Model\UsersTable');
        $this->setExpectedException('Application\Exception\RuntimeException');
        $table->save(1, array());
    }

    public function testDelete()
    {
        $table = Bootstrap::getServiceManager()->get('Application\Model\UsersTable');
        $gateway = $table->getTable();
        $select = $gateway->getSql()->select();
        $select->columns(array('count' => new \Zend\Db\Sql\Expression('COUNT(*)')));
        $preResult = $gateway->selectWith($select)->current();
        $result = $table->delete(array('id' => 1));
        $this->assertTrue($result);
        $postResult = $gateway->selectWith($select)->current();
        $this->assertEquals($preResult['count'] -1, $postResult['count']);

        $result = $table->delete(array('id' => 1));
        $this->assertFalse($result);
    }

    public function testGetForms()
    {
        $table = Bootstrap::getServiceManager()->get('Application\Model\UsersTable');
        $this->assertInstanceOf('Zend\Form\Form', $table->getForm('login'));
        $this->assertInstanceOf('Zend\Form\Form', $table->getForm('create'));
        $this->assertInstanceOf('Zend\Form\Form', $table->getForm('edit'));
    }

/*    public function testAuthtentication()
    {
        $table = Bootstrap::getServiceManager()->get('Application\Model\UsersTable');
        $result = $table->authenticate(array('username' => 'felipe.silvacunha@gmail.com', 'password' => '123'));
        $this->assertInstanceOf('Zend\Authentication\Result', $result);
        $this->assertEquals(\Zend\Authentication\Result::SUCCESS, $result->getCode());

        $result = $table->authenticate(array('username' => 'felipe.silvacunha@gmail.com', 'password' => ''));
        $this->assertInstanceOf('Zend\Authentication\Result', $result);
        $this->assertNotEquals(\Zend\Authentication\Result::SUCCESS, $result->getCode());
    }*/
}
