<?php

namespace Application\Form;

use Zend\InputFilter;

use Application\Validator;

class OrderFilter extends InputFilter\InputFilter
{

    use \Zend\ServiceManager\ServiceLocatorAwareTrait;

    public function __construct($serviceLocator)
    {
        $this->setServiceLocator($serviceLocator);

        $this->add([
            'name' => 'client_id',
            'required' => true,
            'validators' => [
                new Validator\Exists($this->getServiceLocator()->get('Application\Model\ClientsTable'))
            ]
        ])->add([
            'name' => 'charge_address_id',
            'required' => true,
            'validators' => [
                new Validator\Exists($this->getServiceLocator()->get('Application\Model\AddressesTable'))
            ]
        ])->add([
            'name' => 'deliver_address_id',
            'required' => true,
            'validators' => [
                new Validator\Exists($this->getServiceLocator()->get('Application\Model\AddressesTable'))
            ]
        ])->add([
            'name' => 'payment_form_id',
            'required' => true,
            'validators' => [
                new Validator\Exists($this->getServiceLocator()->get('Application\Model\PaymentsFormsTable'))
            ]
        ]);

        $collection = new InputFilter\CollectionInputFilter();
        $collection->setInputFilter(new OrderItemFilter($this->getServiceLocator()))
            ->setIsRequired(true);

        $this->add($collection, 'items');
    }
}
