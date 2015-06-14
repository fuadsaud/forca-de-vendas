<?php

namespace Application\Form;

use Zend\InputFilter;;

class PaymentFilter extends InputFilter\InputFilter
{

    public function __construct()
    {
        $this->add([
            'name' => 'name',
            'required' => true
        ]);

        $collection = new InputFilter\CollectionInputFilter();
        $collection->setInputFilter(new PaymentFormFilter())
            ->setIsRequired(true);
        $this->add($collection, 'forms');

    }
}
