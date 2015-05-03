<?php

use Phinx\Migration\AbstractMigration;

class Products extends AbstractMigration
{
    public function change()
    {
        $products = $this->table('products');

        $products->addColumn('name', 'string')
            ->addColumn('description', 'text')
            ->addColumn('active', 'boolean',array('default' => 1))
            ->addColumn('stock_quantity', 'integer')
            ->create();

        $prices = $this->table('prices');
        $prices->addColumn('price', 'decimal', array('precision' => 12, 'scale' => 2))
            ->addColumn('initial_date', 'timestamp', array('default' => 'CURRENT_TIMESTAMP'))
            ->addColumn('final_date', 'timestamp', array('null' => true))
            ->addColumn('product_id', 'integer')
            ->addForeignKey('product_id', 'products', 'id')
            ->create();

    }
}
