<?php

namespace Application\Model;

use Zend\Db\TableGateway\TableGatewayInterface;
use Zend\Paginator;

use Application\Exception;

abstract class AbstractTable implements TableInterface
{

use \Zend\ServiceManager\ServiceLocatorAwareTrait;

    protected $forms = array();

    public function __construct(TableGatewayInterface $table, $sl)
    {
        $this->setTable($table);
        $this->setServiceLocator($sl);
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
            try {
                $table->insert($data);
            } catch (\Exception $e) {
                throw new Exception\RuntimeException($e->getMessage());
            }
            $id = $table->getLastInsertValue();
        } else {
            $entry = $this->find($id);
            $entry = array_merge($entry->getArrayCopy(), $data);
            unset($entry['id']);
            try {
                $table->update($data, array('id' => $id));
            } catch (\Exception $e) {
                throw new Exception\RuntimeException($e->getMessage());
            }
        }

        return $id;
    }

    public function find($id)
    {
        $result = $this->getTable()->select(array('id' => $id))->current();
        if (!$result) {
            throw new Exception\UnknowRegistryException();
        }

        return $result;
    }

    public function fetchAll($where = null, array $options = array())
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
            $result = $this->getTable()->select($where);
        }

        return $result;
    }

    public function delete($where)
    {
        return (bool)$this->getTable()->delete($where);
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
