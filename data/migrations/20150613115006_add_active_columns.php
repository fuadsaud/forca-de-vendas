<?php

use Phinx\Migration\AbstractMigration;

class AddActiveColumns extends AbstractMigration
{
    public function change()
    {
        $categories = $this->table('categories');
        $categories->addColumn('active', 'boolean', ['default' => true]);
        $categories->save();
    }
}
