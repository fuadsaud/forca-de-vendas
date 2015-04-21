<?php

namespace ApplicationTest\Fixture;

class Users extends AbstractFixture
{
    protected function getItems()
    {
        return array(
            array(
                'id' => 1,
                'name' => 'Luiz',
                'email' => 'felipe.silvacunha@gmail.com',
                'password' => '123',
                'group_id' => 1,
            ),
        );
    }
}
