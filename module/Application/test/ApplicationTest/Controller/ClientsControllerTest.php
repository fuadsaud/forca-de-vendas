<?php

namespace ApplicationTest\Controller;

use ApplicationTest\Bootstrap;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class ClientsControllerTest extends AbstractHttpControllerTestCase
{
    public function setUp()
    {
        $this->setApplicationConfig(Bootstrap::getServiceManager()->get('ApplicationConfig'));
        Bootstrap::getServiceManager()->get('FixturesRunner')->uses(array('clients'));
        parent::setUp();
    }

    public function testGetList()
    {
        $this->dispatch('/api/clients');
        $this->assertControllerName('Application\Api\Controller\Clients');
        $this->assertResponseStatusCode(200);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $json = json_decode($this->getResponse()->getBody(), true);
        $this->assertArrayHasKey('clients', $json);
        $this->assertNotEmpty($json['clients']);
        $this->assertArrayHasKey('pages', $json);
        $this->assertArrayHasKey('page', $json);
        $this->assertArrayHasKey('messages', $json);
    }

    public function testGetSortedList()
    {
        $this->dispatch('/api/clients', 'GET', array('sort' => 'name', 'order' => 'DESC'));
        $this->assertControllerName('Application\Api\Controller\Clients');
        $this->assertResponseStatusCode(200);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $json = json_decode($this->getResponse()->getBody(), true);
        $this->assertArrayHasKey('clients', $json);
        $this->assertNotEmpty($json['clients']);

        $this->reset();
        $this->dispatch('/api/clients', 'GET', array('sort' => 'name'));
        $this->assertControllerName('Application\Api\Controller\Clients');
        $this->assertResponseStatusCode(200);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $json = json_decode($this->getResponse()->getBody(), true);
        $this->assertArrayHasKey('clients', $json);
        $this->assertNotEmpty($json['clients']);
    }

    public function testGetListWithSize()
    {
        $this->dispatch('/api/clients', 'GET', array('size' => '1'));
        $this->assertControllerName('Application\Api\Controller\Clients');
        $this->assertResponseStatusCode(200);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $json = json_decode($this->getResponse()->getBody(), true);
        $this->assertArrayHasKey('clients', $json);
        $this->assertNotEmpty($json['clients']);
        $this->assertCount(1, $json['clients']);
    }

    public function testGetListWithInvalidSize()
    {
        $this->dispatch('/api/clients', 'GET', array('size' => '-1'));
        $this->assertControllerName('Application\Api\Controller\Clients');
        $this->assertResponseStatusCode(200);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $json = json_decode($this->getResponse()->getBody(), true);
        $this->assertArrayHasKey('clients', $json);
        $this->assertNotEmpty($json['clients']);
    }

    public function testGetUnpaginatedList()
    {
        $this->dispatch('/api/clients', 'GET', array('show_all' => ''));
        $this->assertControllerName('Application\Api\Controller\Clients');
        $this->assertResponseStatusCode(200);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $json = json_decode($this->getResponse()->getBody(), true);
        $this->assertArrayHasKey('clients', $json);
        $this->assertNotEmpty($json['clients']);
    }

    public function testGet()
    {
        $this->dispatch('/api/clients/1');
        $this->assertControllerName('Application\Api\Controller\Clients');
        $this->assertResponseStatusCode(200);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $json = json_decode($this->getResponse()->getBody(), true);
        $this->assertArrayHasKey('client', $json);
        $this->assertArrayHasKey('name', $json['client']);
        $this->assertEquals(1, $json['client']['id']);
    }

    public function testGetInvalidClient()
    {
        $this->dispatch('/api/clients/9999999');
        $this->assertControllerName('Application\Api\Controller\Clients');
        $this->assertResponseStatusCode(404);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $json = json_decode($this->getResponse()->getBody(), true);
        $this->assertArrayHasKey('client', $json);
        $this->assertEmpty($json['client']);
    }

    public function testDelete()
    {
        $clients = $this->getApplicationServiceLocator()->get('Application\Model\ClientsTable');
        $select = $clients->getTable()->getSql()->select();
        $select->columns(array('count' => new \Zend\Db\Sql\Expression('COUNT(*)')));
        $total = $clients->getTable()->selectWith($select)->current();
        $this->dispatch('/api/clients/1', 'DELETE');
        $this->assertControllerName('Application\Api\Controller\Clients');
        $this->assertResponseStatusCode(200);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $totalNew = $clients->getTable()->selectWith($select)->current();
        $this->assertEquals($total->count-1, $totalNew->count);
    }
/*
    public function testDeleteNotAllowedClient()
    {
        $clients = $this->getApplicationServiceLocator()->get('Application\Model\ClientsTable');
        $select = $clients->getTable()->getSql()->select();
        $select->columns(array('count' => new \Zend\Db\Sql\Expression('COUNT(*)')));
        $total = $clients->getTable()->selectWith($select)->current();
        $this->dispatch('/api/clients/1', 'DELETE');
        $this->assertControllerName('Application\Api\Controller\Clients');
        $this->assertResponseStatusCode(400);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $totalNew = $clients->getTable()->selectWith($select)->current();
        $this->assertEquals($total->count, $totalNew->count);
    }
 */
    public function testDeleteInvalidClient()
    {
        $clients = $this->getApplicationServiceLocator()->get('Application\Model\ClientsTable');
        $select = $clients->getTable()->getSql()->select();
        $select->columns(array('count' => new \Zend\Db\Sql\Expression('COUNT(*)')));
        $total = $clients->getTable()->selectWith($select)->current();
        $this->dispatch('/api/clients/99999999', 'DELETE');
        $this->assertControllerName('Application\Api\Controller\Clients');
        $this->assertResponseStatusCode(404);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $totalNew = $clients->getTable()->selectWith($select)->current();
        $this->assertEquals($total->count, $totalNew->count);
    }


    public function testCreateClient()
    {
        $client = array(
            'name' => 'Acougue e Mercearia Mossmann',
            'cnpj' => '43696647000102',
            'trading_name' => 'Acougue e Mercearia Mossmann',
            'email' => 'mossmann@gmail.com'
        );
        $this->dispatch('/api/clients', 'POST', $client);
        $this->assertControllerName('Application\Api\Controller\Clients');
        $this->assertResponseStatusCode(201);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $json = json_decode($this->getResponse()->getBody(), true);
        $this->assertArrayHasKey('id', $json);
        $this->assertTrue(is_numeric($json['id']));
    }

    public function testCreateClientWithError()
    {
        $this->dispatch('/api/clients', 'POST');
        $this->assertResponseStatusCode(400);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $json = json_decode($this->getResponse()->getBody(), true);
        $this->assertArrayHasKey('fields', $json);
        $this->assertNotEmpty($json['fields']);
    }

    public function testEditClient()
    {
        $client = array(
            'name' => 'up client',
            'cnpj' => '43696647000102',
            'trading_name' => 'Acougue e Mercearia Mossmann',
            'email' => 'mossmann@gmail.com'
        );
        $this->dispatch('/api/clients/1', 'PUT', $client);
        $this->assertControllerName('Application\Api\COntroller\Clients');
        $this->assertResponseStatusCode(200);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $json = json_decode($this->getResponse()->getBody(), true);
        $this->assertArrayHasKey('id', $json);
        $this->assertTrue(is_numeric($json['id']));

        $this->reset();
        $this->dispatch('/api/clients/1');
        $json = json_decode($this->getResponse()->getBody(), true);
        $this->assertEquals($json['client']['name'], 'up client');
    }

    public function testEditClientWithError()
    {
        $this->dispatch('/api/clients/1', 'PUT');
        $this->assertResponseStatusCode(400);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $json = json_decode($this->getResponse()->getBody(), true);
        $this->assertArrayHasKey('fields', $json);
        $this->assertNotEmpty($json['fields']);
    }

    public function testEditInvalidClient()
    {
        $client = array(
            'name' => 'up client',
            'cnpj' => '43696647000102',
            'trading_name' => 'Acougue e Mercearia Mossmann',
            'email' => 'mossmann@gmail.com'
        );
        $this->dispatch('/api/clients/9999999', 'PUT', $client);
        $this->assertResponseStatusCode(404);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $json = json_decode($this->getResponse()->getBody(), true);
        $this->assertNotEmpty($json['messages']);
    }

}
