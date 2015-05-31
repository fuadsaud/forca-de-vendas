<?php

namespace Application\Form;

use Zend\InputFilter;

class ClientAddressesFilter extends InputFilter\CollectionInputFilter
{

    use \Zend\ServiceManager\ServiceLocatorAwareTrait;

    public function __construct($serviceLocator)
    {
        $this->setServiceLocator($serviceLocator);
        $this->init();
    }

    public function init()
    {
        $this->setIsRequired(true);
        $input = new AddressFilter($this->getServiceLocator());
        $input->remove('client_id');
        $this->setInputFilter($input);
        $this->setCount(2);
//        var_dump($this->getCount());
//        var_dump(get_class_methods($this));
//        die;
    }

    public function isValid()
    {
        $result = parent::isValid();
        if ($result) {
            $input = new InputFilter\Input();
            $validator = new \Zend\Validator\Callback([$this, 'areValidTypes']);
            $validator->setMessage('Client addresses must be of different types');
            $input->getValidatorChain()->addValidator($validator);
            $input->setValue($this->data);
            if (!$input->isValid()) {
                $this->invalidInputs[0]['type'] = $input;
                $this->collectionMessages[0]['type'] = $input->getMessages();
                $result = false;
            }
        }
        return $result;
    }

    public function areValidTypes($value)
    {
        return $value[0]['type'] != $value[1]['type'];
    }
}
