<?php

namespace Application\Model;

class PaymentsFormsTable extends AbstractTable
{
    public function loadForm($identifier)
    {
        throw new \Exception('method not allowed');
    }

    public function save($id, $data)
    {
        if (empty($data['interest'])) {
            unset($data['interest']);
        }
        return parent::save($id, $data);
    }
}
