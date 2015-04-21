<?php

namespace ApplicationTest\Controller;

use ApplicationTest\Bootstrap;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class GroupsControllerTest extends AbstractHttpControllerTestCase
{
    public function setUp()
    {
        $this->setApplicationConfig(Bootstrap::getServiceManager()->get('ApplicationConfig'));
        Bootstrap::getServiceManager()->get('FixturesRunner')->uses(array('groups', 'users'));
        parent::setUp();
    }

    public function testGetList()
    {
        $this->dispatch('/api/groups');
        $this->assertControllerName('Application\Api\Controller\Groups');
        $this->assertResponseStatusCode(200);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $json = json_decode($this->getResponse()->getBody(), true);
        $this->assertArrayHasKey('groups', $json);
        $this->assertNotEmpty($json['groups']);
        $this->assertArrayHasKey('pages', $json);
        $this->assertArrayHasKey('page', $json);
        $this->assertArrayHasKey('messages', $json);
    }

    public function testGetSortedList()
    {
        $this->dispatch('/api/groups', 'GET', array('sort' => 'name', 'order' => 'DESC'));
        $this->assertControllerName('Application\Api\Controller\Groups');
        $this->assertResponseStatusCode(200);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $json = json_decode($this->getResponse()->getBody(), true);
        $this->assertArrayHasKey('groups', $json);
        $this->assertNotEmpty($json['groups']);

        $this->reset();
        $this->dispatch('/api/groups', 'GET', array('sort' => 'name'));
        $this->assertControllerName('Application\Api\Controller\Groups');
        $this->assertResponseStatusCode(200);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $json = json_decode($this->getResponse()->getBody(), true);
        $this->assertArrayHasKey('groups', $json);
        $this->assertNotEmpty($json['groups']);
    }

    public function testGetListWithSize()
    {
        $this->dispatch('/api/groups', 'GET', array('size' => '1'));
        $this->assertControllerName('Application\Api\Controller\Groups');
        $this->assertResponseStatusCode(200);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $json = json_decode($this->getResponse()->getBody(), true);
        $this->assertArrayHasKey('groups', $json);
        $this->assertNotEmpty($json['groups']);
        $this->assertCount(1, $json['groups']);
    }

    public function testGetListWithInvalidSize()
    {
        $this->dispatch('/api/groups', 'GET', array('size' => '-1'));
        $this->assertControllerName('Application\Api\Controller\Groups');
        $this->assertResponseStatusCode(200);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $json = json_decode($this->getResponse()->getBody(), true);
        $this->assertArrayHasKey('groups', $json);
        $this->assertNotEmpty($json['groups']);
    }

    public function testGetUnpaginatedList()
    {
        $this->dispatch('/api/groups', 'GET', array('show_all' => ''));
        $this->assertControllerName('Application\Api\Controller\Groups');
        $this->assertResponseStatusCode(200);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $json = json_decode($this->getResponse()->getBody(), true);
        $this->assertArrayHasKey('groups', $json);
        $this->assertNotEmpty($json['groups']);
    }

    public function testGet()
    {
        $this->dispatch('/api/groups/1');
        $this->assertControllerName('Application\Api\Controller\Groups');
        $this->assertResponseStatusCode(200);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $json = json_decode($this->getResponse()->getBody(), true);
        $this->assertArrayHasKey('group', $json);
        $this->assertArrayHasKey('name', $json['group']);
        $this->assertEquals(1, $json['group']['id']);
    }

    public function testGetInvalidGroup()
    {
        $this->dispatch('/api/groups/9999999');
        $this->assertControllerName('Application\Api\Controller\Groups');
        $this->assertResponseStatusCode(404);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $json = json_decode($this->getResponse()->getBody(), true);
        $this->assertArrayHasKey('group', $json);
        $this->assertEmpty($json['group']);
    }

    public function testDelete()
    {
        $groups = $this->getApplicationServiceLocator()->get('Application\Model\GroupsTable');
        $select = $groups->getTable()->getSql()->select();
        $select->columns(array('count' => new \Zend\Db\Sql\Expression('COUNT(*)')));
        $total = $groups->getTable()->selectWith($select)->current();
        $this->dispatch('/api/groups/2', 'DELETE');
        $this->assertControllerName('Application\Api\Controller\Groups');
        $this->assertResponseStatusCode(200);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $totalNew = $groups->getTable()->selectWith($select)->current();
        $this->assertEquals($total->count-1, $totalNew->count);
    }

    public function testDeleteNotAllowedGroup()
    {
        $groups = $this->getApplicationServiceLocator()->get('Application\Model\GroupsTable');
        $select = $groups->getTable()->getSql()->select();
        $select->columns(array('count' => new \Zend\Db\Sql\Expression('COUNT(*)')));
        $total = $groups->getTable()->selectWith($select)->current();
        $this->dispatch('/api/groups/1', 'DELETE');
        $this->assertControllerName('Application\Api\Controller\Groups');
        $this->assertResponseStatusCode(400);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $totalNew = $groups->getTable()->selectWith($select)->current();
        $this->assertEquals($total->count, $totalNew->count);
    }

    public function testDeleteInvalidGroup()
    {
        $groups = $this->getApplicationServiceLocator()->get('Application\Model\GroupsTable');
        $select = $groups->getTable()->getSql()->select();
        $select->columns(array('count' => new \Zend\Db\Sql\Expression('COUNT(*)')));
        $total = $groups->getTable()->selectWith($select)->current();
        $this->dispatch('/api/groups/99999999', 'DELETE');
        $this->assertControllerName('Application\Api\Controller\Groups');
        $this->assertResponseStatusCode(404);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $totalNew = $groups->getTable()->selectWith($select)->current();
        $this->assertEquals($total->count, $totalNew->count);
    }


    public function testCreateGroup()
    {
        $this->dispatch('/api/groups', 'POST', array('name' => 'new group'));
        $this->assertControllerName('Application\Api\Controller\Groups');
        $this->assertResponseStatusCode(201);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $json = json_decode($this->getResponse()->getBody(), true);
        $this->assertArrayHasKey('id', $json);
        $this->assertTrue(is_numeric($json['id']));
    }

    public function testCreateGroupWithError()
    {
        $this->dispatch('/api/groups', 'POST');
        $this->assertResponseStatusCode(400);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $json = json_decode($this->getResponse()->getBody(), true);
        $this->assertArrayHasKey('fields', $json);
        $this->assertNotEmpty($json['fields']);
    }

    public function testEditGroup()
    {
        $this->dispatch('/api/groups/1', 'PUT', array('name' => 'up group'));
        $this->assertControllerName('Application\Api\COntroller\Groups');
        $this->assertResponseStatusCode(200);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $json = json_decode($this->getResponse()->getBody(), true);
        $this->assertArrayHasKey('id', $json);
        $this->assertTrue(is_numeric($json['id']));

        $this->reset();
        $this->dispatch('/api/groups/1');
        $json = json_decode($this->getResponse()->getBody(), true);
        $this->assertEquals($json['group']['name'], 'up group');
    }

    public function testEditGroupWithError()
    {
        $this->dispatch('/api/groups/1', 'PUT');
        $this->assertResponseStatusCode(400);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $json = json_decode($this->getResponse()->getBody(), true);
        $this->assertArrayHasKey('fields', $json);
        $this->assertNotEmpty($json['fields']);
    }

    public function testEditInvalidGroup()
    {
        $this->dispatch('/api/groups/9999999', 'PUT', array('name' => 'group'));
        $this->assertResponseStatusCode(404);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $json = json_decode($this->getResponse()->getBody(), true);
        $this->assertNotEmpty($json['messages']);
    }

}
