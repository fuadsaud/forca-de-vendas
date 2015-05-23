<?php

namespace Application\Form;

use Zend\InputFilter\InputFilter;

class ClientFilter extends InputFilter
{

    public function __construct()
    {
        $this->add(array(
            'name' => 'name',
            'required' => true,
        ))->add(array(
            'name' => 'email',
            'required' => true,
            'validators' => array(
                array('name' => 'EmailAddress')
            ),
        ))->add(array(
            'name' => 'trading_name',
            'required' => true,
        ))->add(array(
            'name' => 'cnpj',
            'required' => true,
            'validators' => array(
                array(
                    'name' => 'Callback',
                    'options' => array(
                        'callback' => array($this, 'isValidCnpj'),
                        'message' => 'Invalid CNPJ',
                    ),
                )
            ),
            'filters' => array(
                array('name' => 'Digits')
			),
        ));
    }

    public function isValidCnpj($value)
    {
        $numbers = preg_replace('/[^0-9]/','', $value);
        $result = false;
        if (strlen($numbers) == 14) {
            $newNumbers = substr($numbers, 0, -2);
            $base = array(5,4,3,2,9,8,7,6,5,4,3,2);
            $tot = 0;
            for ($i=0; $i<12; $i++) {
                $tot += $base[$i] * $newNumbers[$i];
            }
            $digit = $tot % 11;
            if ($digit < 2) {
                $digit = 0;
            } else {
                $digit = 11 - $digit;
            }
            $newNumbers .= $digit;
            array_unshift($base, 6);
            $tot = 0;
            for ($i=0; $i<13; $i++) {
                $tot += $base[$i] * $newNumbers[$i];
            }
            $digit = $tot % 11;
            if ($digit < 2) {
                $digit = 0;
            } else {
                $digit = 11 - $digit;
            }
            $newNumbers .= $digit;
            if ($newNumbers == $numbers) {
                $result = true;
            }
        }

        return $result;
    }
}
