<?php

namespace Application\Form;

use Zend\InputFilter\InputFilter;

use Application\Validator;

class OrderItemFilter extends InputFilter
{
    use \Zend\ServiceManager\ServiceLocatorAwareTrait;

    public function __construct($serviceLocator)
    {
        $this->setServiceLocator($serviceLocator);

        $this->add([
            'name' => 'quantity',
            'required' => true,
            'validators' => [
                ['name' => 'Digits']
            ]
        ])->add([
            'name' => 'product_id',
            'required' => true,
            'validators' => [
                new Validator\Exists($this->getServiceLocator()->get('Application\Model\ProductsTable'))
            ]
        ]);
    }
}
