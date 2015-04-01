<?php

use Phinx\Migration\AbstractMigration;

class Orders extends AbstractMigration
{
    public function change()
    {
        $orders = $this->table('orders');
        $orders->addColumn('date', 'timestamp', array('default' => 'CURRENT_TIMESTAMP'))
            ->addColumn('deliver_time', 'integer')
            ->addColumn('client_id', 'integer')
            ->addColumn('charge_address_id', 'integer')
            ->addColumn('deliver_address_id', 'integer')
            ->addColumn('payment_form_id', 'integer')
            ->addForeignKey('client_id', 'clients', 'id')
            ->addForeignKey('charge_address_id', 'addresses', 'id')
            ->addForeignKey('deliver_address_id', 'addresses', 'id')
            ->addForeignKey('payment_form_id', 'payments_forms', 'id')
            ->create();

        $orderItems = $this->table('order_items');
        $orderItems->addColumn('quantity', 'integer')
            ->addColumn('product_id', 'integer')
            ->addColumn('price_id', 'integer')
            ->addColumn('order_id', 'integer')
            ->addForeignKey('product_id', 'products', 'id')
            ->addForeignKey('price_id', 'prices', 'id')
            ->addForeignKey('order_id', 'orders', 'id')
            ->create();
    }
}
