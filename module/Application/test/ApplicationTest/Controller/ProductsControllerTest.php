<?php

namespace ApplicationTest\Controller;

use ApplicationTest\Bootstrap;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class ProductsControllerTest extends AbstractHttpControllerTestCase
{

    use \ApplicationTest\Assert\ArrayCompareTrait;

    public function setUp()
    {
        $this->setApplicationConfig(Bootstrap::getServiceManager()->get('ApplicationConfig'));
        Bootstrap::getServiceManager()->get('FixturesRunner')->uses(array('categories', 'products'));
        parent::setUp();
    }

    public function testGetList()
    {
        $this->dispatch('/api/products');
        $this->assertControllerName('Application\Api\Controller\Products');
        $this->assertResponseStatusCode(200);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $json = json_decode($this->getResponse()->getBody(), true);
        $this->assertArrayHasKey('products', $json);
        $this->assertNotEmpty($json['products']);
        $this->assertArrayHasKey('pages', $json);
        $this->assertArrayHasKey('page', $json);
        $this->assertArrayHasKey('messages', $json);
    }

    public function testGetSortedList()
    {
        $this->dispatch('/api/products', 'GET', array('sort' => 'name', 'order' => 'DESC'));
        $this->assertControllerName('Application\Api\Controller\Products');
        $this->assertResponseStatusCode(200);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $json = json_decode($this->getResponse()->getBody(), true);
        $this->assertArrayHasKey('products', $json);
        $this->assertNotEmpty($json['products']);

        $this->reset();
        $this->dispatch('/api/products', 'GET', array('sort' => 'name'));
        $this->assertControllerName('Application\Api\Controller\Products');
        $this->assertResponseStatusCode(200);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $json = json_decode($this->getResponse()->getBody(), true);
        $this->assertArrayHasKey('products', $json);
        $this->assertNotEmpty($json['products']);
    }

    public function testGetListWithSize()
    {
        $this->dispatch('/api/products', 'GET', array('size' => '1'));
        $this->assertControllerName('Application\Api\Controller\Products');
        $this->assertResponseStatusCode(200);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $json = json_decode($this->getResponse()->getBody(), true);
        $this->assertArrayHasKey('products', $json);
        $this->assertNotEmpty($json['products']);
        $this->assertCount(1, $json['products']);
    }

    public function testGetListWithInvalidSize()
    {
        $this->dispatch('/api/products', 'GET', array('size' => '-1'));
        $this->assertControllerName('Application\Api\Controller\Products');
        $this->assertResponseStatusCode(200);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $json = json_decode($this->getResponse()->getBody(), true);
        $this->assertArrayHasKey('products', $json);
        $this->assertNotEmpty($json['products']);
    }

    public function testGetUnpaginatedList()
    {
        $this->dispatch('/api/products', 'GET', array('show_all' => ''));
        $this->assertControllerName('Application\Api\Controller\Products');
        $this->assertResponseStatusCode(200);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $json = json_decode($this->getResponse()->getBody(), true);
        $this->assertArrayHasKey('products', $json);
        $this->assertNotEmpty($json['products']);
    }

    public function testGet()
    {
        $this->dispatch('/api/products/1');
        $this->assertControllerName('Application\Api\Controller\Products');
        $this->assertResponseStatusCode(200);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $json = json_decode($this->getResponse()->getBody(), true);
        $this->assertArrayHasKey('product', $json);
        $this->assertArrayHasKey('name', $json['product']);
        $this->assertArrayHasKey('categories', $json['product']);
        $this->assertEquals(1, $json['product']['id']);
    }

    public function testGetInvalidProduct()
    {
        $this->dispatch('/api/products/9999999');
        $this->assertControllerName('Application\Api\Controller\Products');
        $this->assertResponseStatusCode(404);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $json = json_decode($this->getResponse()->getBody(), true);
        $this->assertArrayHasKey('product', $json);
        $this->assertEmpty($json['product']);
    }

    public function testDelete()
    {
        $products = $this->getApplicationServiceLocator()->get('Application\Model\ProductsTable');
        $select = $products->getTable()->getSql()->select();
        $select->columns(array('count' => new \Zend\Db\Sql\Expression('COUNT(*)')));
        $total = $products->getTable()->selectWith($select)->current();
        $this->dispatch('/api/products/1', 'DELETE');
        $this->assertControllerName('Application\Api\Controller\Products');
        $this->assertResponseStatusCode(200);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $totalNew = $products->getTable()->selectWith($select)->current();
        $this->assertEquals($total->count-1, $totalNew->count);
    }

/*    public function testDeleteNotAllowedProduct()
    {
        $products = $this->getApplicationServiceLocator()->get('Application\Model\ProductsTable');
        $select = $products->getTable()->getSql()->select();
        $select->columns(array('count' => new \Zend\Db\Sql\Expression('COUNT(*)')));
        $total = $products->getTable()->selectWith($select)->current();
        $this->dispatch('/api/products/1', 'DELETE');
        $this->assertControllerName('Application\Api\Controller\Products');
        $this->assertResponseStatusCode(400);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $totalNew = $products->getTable()->selectWith($select)->current();
        $this->assertEquals($total->count, $totalNew->count);
    }
*/

    public function testDeleteInvalidProduct()
    {
        $products = $this->getApplicationServiceLocator()->get('Application\Model\ProductsTable');
        $select = $products->getTable()->getSql()->select();
        $select->columns(array('count' => new \Zend\Db\Sql\Expression('COUNT(*)')));
        $total = $products->getTable()->selectWith($select)->current();
        $this->dispatch('/api/products/99999999', 'DELETE');
        $this->assertControllerName('Application\Api\Controller\Products');
        $this->assertResponseStatusCode(404);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $totalNew = $products->getTable()->selectWith($select)->current();
        $this->assertEquals($total->count, $totalNew->count);
    }


    public function testCreateProduct()
    {
        $this->dispatch('/api/products', 'POST', $this->getData());
        $this->assertControllerName('Application\Api\Controller\Products');
        $this->assertResponseStatusCode(201);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $json = json_decode($this->getResponse()->getBody(), true);
        $this->assertArrayHasKey('id', $json);
        $this->assertTrue(is_numeric($json['id']));

        $this->reset();
        $this->dispatch('/api/products/'.$json['id']);
        $this->assertResponseStatusCode(200);
        $json = json_decode($this->getResponse()->getBody(), true);
        $this->assertArrayHasKey('product', $json);
        $data = $this->getData();
        $this->assertArrayInto($data, $json['product']);
    }

    public function testCreateProductWithError()
    {
        $this->dispatch('/api/products', 'POST');
        $this->assertResponseStatusCode(400);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $json = json_decode($this->getResponse()->getBody(), true);
        $this->assertArrayHasKey('fields', $json);
        $this->assertNotEmpty($json['fields']);
    }

    public function testEditProduct()
    {
        $this->dispatch('/api/products/1', 'PUT', $this->getData());
        $this->assertControllerName('Application\Api\Controller\Products');
        $this->assertResponseStatusCode(200);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $json = json_decode($this->getResponse()->getBody(), true);
        $this->assertArrayHasKey('id', $json);
        $this->assertTrue(is_numeric($json['id']));
        $this->assertEquals(1, $json['id']);

        $this->reset();
        $this->dispatch('/api/products/'.$json['id']);
        $this->assertResponseStatusCode(200);
        $json = json_decode($this->getResponse()->getBody(), true);
        $this->assertArrayHasKey('product', $json);
        $data = $this->getData();
        $this->assertArrayInto($data, $json['product']);
    }

    public function testEditProductWithError()
    {
        $this->dispatch('/api/products/1', 'PUT');
        $this->assertResponseStatusCode(400);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $json = json_decode($this->getResponse()->getBody(), true);
        $this->assertArrayHasKey('fields', $json);
        $this->assertNotEmpty($json['fields']);
    }

    public function testEditInvalidProduct()
    {
        $this->dispatch('/api/products/9999999', 'PUT', $this->getData());
        $this->assertResponseStatusCode(404);
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        $json = json_decode($this->getResponse()->getBody(), true);
        $this->assertNotEmpty($json['messages']);
    }

    protected function getData()
    {
        return array(
            'name' => 'Product',
            'price' => '12.00',
            'description' => 'Description',
            'active' => 1,
            'stock_quantity' => 10,
            'categories' => array(1),
        );
    }

}
