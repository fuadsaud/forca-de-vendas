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
                $form->setInputFilter(new Form\ProductFilter());
                break;
        }
        return $form;
    }

    public function save($id, $data)
    {
        $categories = null;
        if (isset($data['categories']) && is_array($data['categories'])) {
           $categories = $data['categories'];
           unset($data['categories']);
        }
        try {
            $this->beginTransaction();
            $id = parent::save($id, $data);

            if (!is_null($categories)) {
                $this->syncCategories($id, $categories);
            }
            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            throw new Exception\RuntimeException($e->getMessage());
        }

        return $id;
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
