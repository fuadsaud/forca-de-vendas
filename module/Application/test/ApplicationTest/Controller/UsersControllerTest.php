<?php

namespace ApplicationTest\Controller;

use ApplicationTest\Bootstrap;
use PHPUnit_Framework_TestCase;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
class UsersControllerTest extends AbstractHttpControllerTestCase
{
    public function setUp()
    {
        $this->setApplicationConfig(Bootstrap::getServiceManager()->get('ApplicationConfig'));
        parent::setUp();
    }

    public function testGetList()
    {
        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);

        $adapter = new Zend\Db\Adapter\Adapter(array(
            'driver' => 'Mysqli',
            'database' => 'forca_de_vendas',
            'username' => 'root',
            'password' => '123'
         ));

        $serviceManager->setService('Zend\Db\Adapter\Adapter', );



        $this->dispatch('/api/users');
        $this->assertControllerName('Application\Api\Controller\Users');
        $this->assertResponseStatusCode(200);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $json = json_decode($this->getResponse()->getBody(), true);
        $this->assertArrayHasKey('users', $json);
        $this->assertArrayHasKey('pages', $json);
        $this->assertArrayHasKey('page', $json);
        $this->assertArrayHasKey('messages', $json);
    }

    public function testGet()
    {
    }
}
