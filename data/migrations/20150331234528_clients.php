<?php

use Phinx\Migration\AbstractMigration;

class Clients extends AbstractMigration
{
    public function change()
    {
        $clients = $this->table('clients');
        $clients->addColumn('name', 'string')
            ->addColumn('cnpj', 'string')
            ->addColumn('trading_name', 'string')
            ->addColumn('email', 'string')
            ->create();
    }
}
