<?php

use Phinx\Migration\AbstractMigration;

class Users extends AbstractMigration
{
    public function change()
    {
        $groups = $this->table('groups');
        $groups->addColumn('name', 'string')
            ->create();

        $users = $this->table('users');
        $users->addColumn('email', 'string')
            ->addColumn('password', 'string')
            ->addColumn('name', 'string')
            ->addColumn('group_id', 'integer')
            ->addForeignKey('group_id', 'groups', 'id')
            ->create();

    }
}
