<?php

namespace ApplicationTest\Fixture;

class Products extends AbstractFixture
{
    protected function getItems()
    {
        return array(
            array(
                'id' => 1,
                'name' => 'Product 1',
                'description' => 'Product Description',
                'active' => 1,
                'stock_quantity' => 10,
                'categories' => array(1),
            ),
        );
    }
}
