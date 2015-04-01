<?php

use Phinx\Migration\AbstractMigration;

class Payments extends AbstractMigration
{
    public function change()
    {
        $payments = $this->table('payments');
        $payments->addColumn('name', 'string')
            ->addColumn('image_path', 'string')
            ->create();

        $paymentsForms = $this->table('payments_forms');
        $paymentsForms->addColumn('description', 'string')
            ->addColumn('installments', 'integer')
            ->addColumn('payment_id', 'integer')
            ->addColumn('interest', 'decimal', array('precision' => 12, 'scale' => 2))
            ->addForeignKey('payment_id', 'payments', 'id')
            ->create();
    }
}
