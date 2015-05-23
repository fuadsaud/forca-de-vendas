<?php

namespace Application\Model;

use Application\Form;
use Application\Exception;

class ProductsTable extends AbstractTable
{
    protected function loadForm($identifier)
    {
        $form = null;
        switch ($identifier) {
            case 'edit':
            case 'create':
                $form = new Form\ProductForm();
                $form->setInputFilter(new Form\ProductFilter($this->getServiceLocator()));
                break;
        }
        return $form;
    }

    public function save($id, $data)
    {
        $categories = null;
        if (isset($data['categories'])) {
            $categories = $data['categories'];
            if (!is_array($categories)) {
                $categories = [$categories];
            }
            unset($data['categories']);
        }
        $price = null;
        if (isset($data['price'])) {
            $price = $data['price'];
            unset($data['price']);
        }

        $this->getServiceLocator()->get('Logger')->debug('PRICE: '.$price);

        try {
            $this->beginTransaction();
            $id = parent::save($id, $data);

            if (!is_null($categories)) {
                $this->syncCategories($id, $categories);
            }

            if (!is_null($price)) {
                $this->setPrice($id, $price);
            }
            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            if ($e instanceof Exception\UnknowRegistryException) {
                throw $e;
            }
            throw new Exception\RuntimeException($e->getMessage());
        }

        return $id;
    }

    public function setPrice($id, $price)
    {
        $pricesTable = $this->getServiceLocator()->get('ProductsPrices');
        $date = date('Y-m-dTH:i:s');
        $pricesTable->update(array('final_date' => $date), array('product_id' => $id));
        $data = array('product_id' => $id, 'price' => (float)$price, 'initial_date' => $date);
        $pricesTable->insert($data);

        return $this;
    }

    public function find($id)
    {
        $result = parent::find($id);
        $result['price'] = $this->getPrice($id);
        $result['categories'] = $this->getCategoriesIds($id);
        return $result;
    }

    public function getPrice($id)
    {
        $pricesTable = $this->getServiceLocator()->get('ProductsPrices');
        $price = $pricesTable->select(array(
            'product_id' => $id,
            new \Zend\Db\Sql\Predicate\IsNull('final_date')
        ))->current();

        return $price['price'];
    }

    public function getCategoriesIds($id)
    {
        $table = $this->getServiceLocator()->get('ProductsCategories');
        $categories = $table->select(array('product_id' => $id));
        return array_map(function($cat) { return $cat['category_id']; }, $categories->toArray());
    }


    public function syncCategories($id, array $categories)
    {
        $table = $this->getServiceLocator()->get('ProductsCategories');
        $table->delete(array('product_id' => $id));
        foreach ($categories as $category_id) {
            $table->insert(array('product_id' => $id, 'category_id' => $category_id));
        }
        return $this;
    }

    public function delete($where)
    {
        $table = $this->getTable();
        $select = $table->getSql()->select();
        $select->columns(array('id'))->where($where);
        $ids = $table->selectWith($select)->toArray();
        $result = false;
        try {
            $this->beginTransaction();
            if (!empty($ids)) {
                $ids = array_map(function($a) { return $a['id'];}, $ids);
                $productsCategories = $this->getServiceLocator()->get('ProductsCategories');
                $productsCategories->delete(array('product_id' => $ids));
                $pricesTable = $this->getServiceLocator()->get('ProductsPrices');
                $pricesTable->delete(array('product_id' => $ids));
            }
            $result = parent::delete($where);
            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            throw new Exception\RuntimeException($e->getMessage());
        }
        return $result;
    }
}
