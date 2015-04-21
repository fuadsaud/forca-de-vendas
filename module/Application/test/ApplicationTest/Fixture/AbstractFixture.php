<?php

namespace ApplicationTest\Fixture;

use Application\Model\TableInterface;

abstract class AbstractFixture implements FixtureInterface
{
    protected $table;

    abstract protected function getItems();

    public function setTable(TableInterface $table)
    {
        $this->table = $table;
    }

    public function getTable()
    {
        return $this->table;
    }

    public function init()
    {
        $table = $this->getTable();
        foreach($this->getItems() as $item) {
            $table->save(null, $item);
        }
        return $this;
    }

    public function clean()
    {
        $this->getTable()->delete(array());
        return $this;
    }

}
