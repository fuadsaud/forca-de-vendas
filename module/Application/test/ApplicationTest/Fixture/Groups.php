<?php

namespace ApplicationTest\Fixture;

class Groups extends AbstractFixture
{
    public function getItems()
    {
        return array(
            array('id' => 1, 'name' => 'test'),
            array('id' => 2, 'name' => 'exclude'),
        );
    }
}
