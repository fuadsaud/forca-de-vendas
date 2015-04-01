<?php

use Phinx\Migration\AbstractMigration;

class Categories extends AbstractMigration
{
    public function change()
    {
        $categories = $this->table('categories');
        $categories->addColumn('name', 'string')
            ->create();

        $productsCategories = $this->table(
            'products_categories',
            array('id' => array('product_id', 'category_id'))
        );

        $productsCategories->addColumn('product_id', 'integer')
            ->addColumn('category_id', 'integer')
            ->addForeignKey('product_id', 'products', 'id')
            ->addForeignKey('category_id', 'categories', 'id')
            ->create();
    }
}
