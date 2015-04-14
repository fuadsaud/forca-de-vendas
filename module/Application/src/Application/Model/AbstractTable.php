<?php

namespace Application\Model;

use Zend\Db\TableGateway\TableGatewayInterface;
use Zend\Paginator;

abstract class AbstractTable implements TableInterface
{

    public function __construct(TableGatewayInterface $table)
    {
        $this->setTable($table);
    }

    public function setTable(TableGatewayInterface $table)
    {
        $this->table = $table;
    }

    public function getTable()
    {
        return $this->table;
    }

    public function save($id, $data)
    {
        $table = $this->getTable();
        if (is_null($id)) {
            $table->insert($data);
            $id = $table->getLastInsertValue();
        } else {
            $table->update($data, array('id' => $id));
        }

        return $id;
    }

    public function find($id)
    {
        return $this->getTable()->select(array('id' => $id));
    }

    public function fetchAll($where, $options)
    {
        $defaultOptions = array(
            'page' => 1,
            'paginated' => false,
            'perPage' => 30,
            'order' => null,
        );
        $options = array_merge($defaultOptions, $options);
        if ($options['paginated']) {
            $adapter = new Paginator\Adapter\DbTableGateway($this->getTable(), $where, $options['order']);
            $paginator = new Paginator\Paginator($adapter);
            $paginator->setItemCountPerPage($options['perPage'])
                ->setCurrentPageNumber($options['page']);
            $result = $paginator;
        } else {
            $select = $this->getTable()->select($where);
        }

        return $result;
    }

    public function delete($where)
    {
        return $this->getTable()->delete($where);
    }

    public function getForm($identifier)
    {
        $identifier = (string)$identifier;
        if (!array_key_exists($identifier, $this->forms)) {
            $this->form[$identifier] = $this->loadForm($identifier);
        }
        return $this->form[$identifier];
    }

    abstract protected function loadForm($identifier);
}
