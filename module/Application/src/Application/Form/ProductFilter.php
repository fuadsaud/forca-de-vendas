<?php

namespace Application\Form;

use Zend\InputFilter\InputFilter;

use Application\Validator;

class ProductFilter extends InputFilter
{

    use \Zend\ServiceManager\ServiceLocatorAwareTrait;

    public function __construct($serviceLocator)
    {
        $this->setServiceLocator($serviceLocator);
        $this->add([
            'name' => 'name',
            'required' => true,
        ])->add([
            'name' => 'description',
            'required' => true,
        ])->add([
            'name' => 'price',
            'required' => true,
            'validators' => [
                ['name' => 'Regex', 'options' => ['pattern' => '/^\d+(\.\d{2}){0,1}$/']]
            ],
        ])->add([
            'name' => 'stock_quantity',
            'required' => true,
        ])->add([
            'name' => 'categories',
            'required' => true,
            'validators' => [
                [ 'name' => 'Callback', 'options' => [ 'callback' => array($this, 'validateCategories') ]]
            ]
        ]);
    }

    public function validateCategories($value)
    {
        if (!is_array($value)) {
            $value = [$value];
        }

        $table = $this->getServiceLocator()->get('Application\Model\CategoriesTable');
        $validator = new Validator\Exists($table);
        foreach ($value as $val) {
            if (!$validator->isValid($val)) {
                return false;
            }
        }

        return true;
    }
}
