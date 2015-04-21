<?php

namespace ApplicationTest\Controller;

use ApplicationTest\Bootstrap;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class UsersControllerTest extends AbstractHttpControllerTestCase
{
    public function setUp()
    {
        $this->setApplicationConfig(Bootstrap::getServiceManager()->get('ApplicationConfig'));
        Bootstrap::getServiceManager()->get('FixturesRunner')->uses(array('groups', 'users'));
        parent::setUp();
    }

    public function testGetList()
    {
        $this->dispatch('/api/users');
        $this->assertControllerName('Application\Api\Controller\Users');
        $this->assertResponseStatusCode(200);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $json = json_decode($this->getResponse()->getBody(), true);
        $this->assertArrayHasKey('users', $json);
        $this->assertNotEmpty($json['users']);
        $this->assertArrayHasKey('pages', $json);
        $this->assertArrayHasKey('page', $json);
        $this->assertArrayHasKey('messages', $json);
    }

    public function testGetSortedList()
    {
        $this->dispatch('/api/users', 'GET', array('sort' => 'name', 'order' => 'DESC'));
        $this->assertControllerName('Application\Api\Controller\Users');
        $this->assertResponseStatusCode(200);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $json = json_decode($this->getResponse()->getBody(), true);
        $this->assertArrayHasKey('users', $json);
        $this->assertNotEmpty($json['users']);

        $this->reset();
        $this->dispatch('/api/users', 'GET', array('sort' => 'name'));
        $this->assertControllerName('Application\Api\Controller\Users');
        $this->assertResponseStatusCode(200);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $json = json_decode($this->getResponse()->getBody(), true);
        $this->assertArrayHasKey('users', $json);
        $this->assertNotEmpty($json['users']);
    }

    public function testGetListWithSize()
    {
        $this->dispatch('/api/users', 'GET', array('size' => '2'));
        $this->assertControllerName('Application\Api\Controller\Users');
        $this->assertResponseStatusCode(200);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $json = json_decode($this->getResponse()->getBody(), true);
        $this->assertArrayHasKey('users', $json);
        $this->assertNotEmpty($json['users']);
        $this->assertCount(1, $json['users']);
    }

    public function testGetListWithInvalidSize()
    {
        $this->dispatch('/api/users', 'GET', array('size' => '-1'));
        $this->assertControllerName('Application\Api\Controller\Users');
        $this->assertResponseStatusCode(200);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $json = json_decode($this->getResponse()->getBody(), true);
        $this->assertArrayHasKey('users', $json);
        $this->assertNotEmpty($json['users']);
    }

    public function testGetUnpaginatedList()
    {
        $this->dispatch('/api/users', 'GET', array('show_all' => ''));
        $this->assertControllerName('Application\Api\Controller\Users');
        $this->assertResponseStatusCode(200);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $json = json_decode($this->getResponse()->getBody(), true);
        $this->assertArrayHasKey('users', $json);
        $this->assertNotEmpty($json['users']);
    }

    public function testGet()
    {
        $this->dispatch('/api/users/1');
        $this->assertControllerName('Application\Api\Controller\Users');
        $this->assertResponseStatusCode(200);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $json = json_decode($this->getResponse()->getBody(), true);
        $this->assertArrayHasKey('user', $json);
        $this->assertArrayHasKey('name', $json['user']);
        $this->assertEquals('Luiz', $json['user']['name']);
        $this->assertEquals(1, $json['user']['id']);
    }

    public function testGetInvalidUser()
    {
        $this->dispatch('/api/users/9999999');
        $this->assertControllerName('Application\Api\Controller\Users');
        $this->assertResponseStatusCode(404);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $json = json_decode($this->getResponse()->getBody(), true);
        $this->assertArrayHasKey('user', $json);
        $this->assertEmpty($json['user']);
    }

    public function testDelete()
    {
        $users = $this->getApplicationServiceLocator()->get('Application\Model\UsersTable');
        $select = $users->getTable()->getSql()->select();
        $select->columns(array('count' => new \Zend\Db\Sql\Expression('COUNT(*)')));
        $total = $users->getTable()->selectWith($select)->current();
        $this->dispatch('/api/users/1', 'DELETE');
        $this->assertControllerName('Application\Api\Controller\Users');
        $this->assertResponseStatusCode(200);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $totalNew = $users->getTable()->selectWith($select)->current();
        $this->assertEquals($total->count-1, $totalNew->count);
    }

    public function testDeleteInvalidUser()
    {
        $users = $this->getApplicationServiceLocator()->get('Application\Model\UsersTable');
        $select = $users->getTable()->getSql()->select();
        $select->columns(array('count' => new \Zend\Db\Sql\Expression('COUNT(*)')));
        $total = $users->getTable()->selectWith($select)->current();
        $this->dispatch('/api/users/99999999', 'DELETE');
        $this->assertControllerName('Application\Api\Controller\Users');
        $this->assertResponseStatusCode(404);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $totalNew = $users->getTable()->selectWith($select)->current();
        $this->assertEquals($total->count, $totalNew->count);
    }


    public function testCreateUser()
    {
        $this->dispatch('/api/users', 'POST', array('name' => 'Luiz', 'email' => 'felipe.silvacunha@gmail.com', 'password' => '123', 'confirmation' => '123', 'group_id' => 1));
        $this->assertControllerName('Application\Api\Controller\Users');
        $this->assertResponseStatusCode(201);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $json = json_decode($this->getResponse()->getBody(), true);
        $this->assertArrayHasKey('id', $json);
        $this->assertTrue(is_numeric($json['id']));
    }

    public function testCreateUserWithError()
    {
        $this->dispatch('/api/users', 'POST');
        $this->assertResponseStatusCode(400);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $json = json_decode($this->getResponse()->getBody(), true);
        $this->assertArrayHasKey('fields', $json);
        $this->assertNotEmpty($json['fields']);
    }

    public function testEditUser()
    {
        $this->dispatch('/api/users/1', 'PUT', array('name' => 'Luiz Fel'));
        $this->assertControllerName('Application\Api\COntroller\Users');
        $this->assertResponseStatusCode(200);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $json = json_decode($this->getResponse()->getBody(), true);
        $this->assertArrayHasKey('id', $json);
        $this->assertTrue(is_numeric($json['id']));

        $this->reset();
        $this->dispatch('/api/users/1');
        $json = json_decode($this->getResponse()->getBody(), true);
        $this->assertEquals($json['user']['name'], 'Luiz Fel');
    }

    public function testEditUserWithError()
    {
        $this->dispatch('/api/users/1', 'PUT');
        $this->assertResponseStatusCode(400);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $json = json_decode($this->getResponse()->getBody(), true);
        $this->assertArrayHasKey('fields', $json);
        $this->assertNotEmpty($json['fields']);
    }

    public function testEditInvalidUser()
    {
        $this->dispatch('/api/users/9999999', 'PUT', array('name' => 'luiz'));
        $this->assertResponseStatusCode(404);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $json = json_decode($this->getResponse()->getBody(), true);
        $this->assertNotEmpty($json['messages']);
    }

}
