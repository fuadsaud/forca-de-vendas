<?php

use Phinx\Migration\AbstractMigration;

class PaymentIntersetAdjust extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('payments_forms');
        $table->changeColumn('interest', 'decimal', ['precision' => 12, 'scale' => 2, 'default' => 0]);
        $table->save();
    }
}
