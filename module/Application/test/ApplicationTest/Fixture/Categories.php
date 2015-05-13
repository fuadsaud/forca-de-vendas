<?php

namespace ApplicationTest\Fixture;

class Categories extends AbstractFixture
{
    protected function getItems()
    {
        return array(
            array('id' => 1, 'name' => 'cat1'),
            array('id' => 2, 'name' => 'cat2'),
        );
    }
}
