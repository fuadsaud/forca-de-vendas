<?php

use Phinx\Migration\AbstractMigration;

class Addresses extends AbstractMigration
{
    public function change()
    {
        $addresses = $this->table('addresses');
        $addresses->addColumn('type', 'string')
            ->addColumn('street', 'string')
            ->addColumn('number', 'string')
            ->addColumn('zipcode', 'string')
            ->addColumn('neighborhood', 'string')
            ->addColumn('complement', 'string', array('null' => true))
            ->addColumn('city', 'string')
            ->addColumn('state', 'string')
            ->addColumn('country', 'string')
            ->addColumn('phone_number', 'string')
            ->addColumn('client_id', 'integer')
            ->addForeignKey('client_id', 'clients', 'id')
            ->create();
    }
}
