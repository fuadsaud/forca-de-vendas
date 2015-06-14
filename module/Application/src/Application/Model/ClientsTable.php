<?php

namespace Application\Model;

use Application\Form;

class ClientsTable extends AbstractTable
{
    protected function loadForm($identifier)
    {
        $form = null;
        switch ($identifier) {
            case 'edit':
            case 'create':
                $form = new Form\ClientForm();
                $form->setInputFilter(new Form\ClientFilter($this->getServiceLocator()));
                break;
        }
        return $form;
    }

    public function filterData($client)
    {
        $addressTable = $this->getServiceLocator()->get('Application\Model\AddressesTable');
        $addresses = $addressTable->fetchAll(['client_id' => $client['id']]);
        $client['addresses'] = $addresses->toArray();
        return $client;
    }

    public function save($id, $data) {
        try {
            $this->beginTransaction();
            $addresses = [];
            if (array_key_exists('addresses', $data)) {
                $addresses = (array)$data['addresses'];
                unset($data['addresses']);
            }
            $id = parent::save($id, $data);
            if (!empty($addresses)) {
                $addressesTable = $this->getServiceLocator()->get('Application\Model\AddressesTable')->getTable();
                $addressesTable->delete(array('client_id' => $id));
                $addressTable = $this->getServiceLocator()->get('Application\Model\AddressesTable');
                foreach ($addresses as $address) {
                    $address['client_id'] = $id;
                    $addressTable->save(null, $address);
                }
            }
            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            throw $e;
        }
        return $id;

    }

    public function delete($where)
    {
        $table = $this->getTable();
        $select = $table->getSql()->select();
        $select->columns(array('id'))->where($where);
        $ids = $table->selectWith($select)->toArray();
        $result = false;
        try {
            $this->beginTransaction();
            if (!empty($ids)) {
                $ids = array_map(function($a) { return $a['id'];}, $ids);
                $addressesTable = $this->getServiceLocator()->get('Application\Model\AddressesTable')->getTable();
                $addressesTable->delete(array('client_id' => $ids));
            }
            $result = parent::delete($where);
            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            throw new Exception\RuntimeException($e->getMessage());
        }
        return $result;
    }
}
