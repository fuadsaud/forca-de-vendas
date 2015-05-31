<?php

namespace Application\Model;

use Application\Form;

class AddressesTable extends AbstractTable
{

    const DELIVERY = 'delivery';

    const BILLING = 'billing';

    public static $TYPES = [ self::DELIVERY, self::BILLING ];

    protected function loadForm($identifier)
    {
        switch ($identifier) {
            default:
                $form = new Form\AddressForm();
                $form->setInputFilter(new Form\AddressFilter($this->getServiceLocator()));
        }

        return $form;
    }

    public function delete($where)
    {
        return false;
    }
}
