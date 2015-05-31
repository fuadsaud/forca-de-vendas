<?php

namespace Application\Form;

use Zend\InputFilter\InputFilter;

class AddressFilter extends InputFilter
{

    use \Zend\ServiceManager\ServiceLocatorAwareTrait;

    public function __construct($serviceLocator)
    {

        $this->setServiceLocator($serviceLocator);

        $serviceLocator = $this->getServiceLocator();

        $validator = new \Application\Validator\Exists($serviceLocator->get('Application\Model\ClientsTable'));

        $this->add([
            'name' => 'type',
            'required' => true,
            'validators' => [
                [
                    'name' => 'InArray',
                    'options' => [
                        'haystack' => [
                            \Application\Model\AddressesTable::DELIVERY,
                            \Application\Model\AddressesTable::BILLING,
                        ]
                    ]
                ]
            ]
        ])->add([
            'name' => 'street',
            'required' => true,
        ])->add([
            'name' => 'number',
            'required' => true,
        ])->add([
            'name' => 'zipcode',
            'required' => true,
            'validators' => [
                [ 'name' => 'Digits' ],
                [
                    'name' => 'StringLength',
                    'options' => [ 'min' => 8, 'max' => 8 ]
                ]
            ]
        ])->add([
            'name' => 'complement',
            'required' => false,
        ])->add([
            'name' => 'neighborhood',
            'required' => true,
        ])->add([
            'name' => 'city',
            'required' => true,
        ])->add([
            'name' => 'state',
            'required' => true,
        ])->add([
            'name' => 'country',
            'required' => true,
        ])->add([
            'name' => 'client_id',
            'required' => true,
            'validators' => [
                $validator
            ]
        ]);
    }
}
