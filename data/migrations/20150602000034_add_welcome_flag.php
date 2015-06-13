<?php

use Phinx\Migration\AbstractMigration;

class AddWelcomeFlag extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('users');
        $table->addColumn('new_user', 'boolean', [ 'default' => true ]);
        $table->save();
    }
}
