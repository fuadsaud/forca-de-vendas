<?php

namespace ApplicationTest\Fixture;

class Clients extends AbstractFixture
{
    protected function getItems()
    {
        return array(
            array(
                'id' => 1,
                'name' => 'Mercearia Mossmann',
                'cnpj' => '415415331000160',
                'trading_name' => 'mercearia Mossmann',
                'email' => 'mosfefewann@gmail.com'
            )
        );
    }
}
