<?php

namespace ApplicationTest\Model;

class ClientsTableTest extends AbstractTable
{
    protected function getTable()
    {
        return $this->getServiceManager()->get('Application\Model\ClientsTable');
    }

    protected function uses()
    {
        return array('clients');
    }

    protected function getCreateData()
    {
        return array(
            'name' => 'Acougue e Mercearia Mossmann',
            'cnpj' => '4369664700002',
            'trading_name' => 'Acougue e Mercearia Mossmann',
            'email' => 'mossmann@gmail.com'
        );
    }

    protected function getUpdateData()
    {
        return array(
            'id' => 1,
            'name' => 'Acougue  Mossmann',
            'cnpj' => '4369664700002',
            'trading_name' => 'Acougue Mossmann',
            'email' => 'mossmann@gmail.com'
        );
    }

    protected function getDeletableId()
    {
        return 1;
    }

    protected function getUndeletableId()
    {
        return 2;
    }

   }
