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
                $form = new Form\ClientEditForm();
                $form->setInputFilter(new Form\ClientEditFilter());
            case 'create':
                $form = new Form\ClientForm();
                $form->setInputFilter(new Form\ClientFilter($this->getServiceLocator()));
                break;
        }
        return $form;
    }

    public function save($id, $data) {
        if (is_null($id)) {
            try {
                $this->beginTransaction();
                $addresses = [];
                if (array_key_exists('addresses', $data)) {
                    $addresses = (array)$data['addresses'];
                    unset($data['addresses']);
                }
                $id = parent::save(null, $data);
                if (!empty($addresses)) {
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
        } else {
            $id = parent::save($id, $data);
        }

        return $id;

    }

/*    public function delete($where)
    {
        $table = $this->getTable();
        $subSelect = $table->getSql()->select();
        $subSelect->where($where);

        $userTable = $this->getServiceLocator()->get('Application\Model\UsersTable');
        $select = $userTable->getTable()->getSql()->select();
        $select
            ->columns(array('count' => new \Zend\Db\Sql\Expression('COUNT(*)')))
            ->join(array('g' => $subSelect), 'g.id = users.group_id', array());

        $result = $table->selectWith($select)->current();
        if ($result['count'] > 0) {
            return false;
        }

        return parent::delete($where);
    }*/
}
