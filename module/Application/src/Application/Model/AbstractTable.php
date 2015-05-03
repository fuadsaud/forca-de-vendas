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

    public function getConnection()
    {
        return $this->getTable()->getAdapter()->getDriver()->getConnection();
    }

    public function beginTransaction()
    {
        $this->getServiceLocator()->get('Logger')->debug('Begin Transaction');
        $this->getConnection()->beginTransaction();
        return $this;
    }

    public function commit()
    {
        $this->getServiceLocator()->get('Logger')->debug('commit');
        $this->getConnection()->commit();
        return $this;
    }

    public function rollback()
    {
        $this->getServiceLocator()->get('Logger')->debug('rollback');
        $this->getConnection()->rollback();
        return $this;
    }

    public function save($id, $data)
    {
        $this->getServiceLocator()->get('Logger')->debug('Saving '.get_class($this). ' id '. $id);
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
        $this->getServiceLocator()->get('Logger')->debug('Findind '.get_class($this).' by id '. $id);
        $result = $this->getTable()->select(array('id' => $id))->current();
        if (!$result) {
            throw new Exception\UnknowRegistryException();
        }

        return $result;
    }

    public function fetchAll($where = null, array $options = array())
    {
        $this->getServiceLocator()->get('Logger')->debug('FetchAll '.get_class($this));
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
        $this->getServiceLocator()->get('Logger')->debug('Deleting '.get_class($this));
        return (bool)$this->getTable()->delete($where);
    }

    public function getForm($identifier)
    {
        $this->getServiceLocator()->get('Logger')->debug('Getting ' . $identifier . ' Form from '.get_class($this));
        $identifier = (string)$identifier;
        if (!array_key_exists($identifier, $this->forms)) {
            $this->form[$identifier] = $this->loadForm($identifier);
        }
        return $this->form[$identifier];
    }

    abstract protected function loadForm($identifier);
}
