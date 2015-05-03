<?php

namespace Application\Model;

use Application\Form;

class CategoriesTable extends AbstractTable
{
    protected function loadForm($identifier)
    {
        $form = null;
        switch ($identifier) {
            case 'edit':
            case 'create':
                $form = new Form\CategoryForm();
                $form->setInputFilter(new Form\CategoryFilter());
                break;
        }
        return $form;
    }

    public function delete($where)
    {
        $this->getServiceLocator()->get('Logger')->debug('Deleting categories');
        $table = $this->getTable();
        $subSelect = $table->getSql()->select();
        $subSelect->where($where);

        $productsCategories = $this->getServiceLocator()->get('ProductsCategories');
        $select = $productsCategories->getSql()->select();
        $select
            ->columns(array('count' => new \Zend\Db\Sql\Expression('COUNT(*)')))
            ->join(array('c' => $subSelect), 'c.id = products_categories.category_id', array());
        $result = $productsCategories->selectWith($select)->current();
        if ($result['count'] > 0) {
            $this->getServiceLocator()->get('Logger')->debug('Cannot delete categories that have associated products');
            return false;
        }
        return parent::delete($where);
    }
}
