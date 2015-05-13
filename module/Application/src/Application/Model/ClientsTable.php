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
                $form->setInputFilter(new Form\ClientFilter());
                break;
        }
        return $form;
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
