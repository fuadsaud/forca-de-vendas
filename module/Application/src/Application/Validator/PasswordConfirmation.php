<?php

namespace Application\Validator;

use Zend\Validator\AbstractValidator;

class PasswordConfirmation extends AbstractValidator
{
    const NOT_EQUALS = 'not_equals';

    protected $relatedValue = "";

    protected $messageTemplates = array(
        self::NOT_EQUALS => "Confirmation '%value%' is not equals to '%relatedValue%'",
    );

    public function setField($field)
    {
        $this->field = (string)$field;
        return $this;
    }

    public function getRelatedValue($context)
    {
        $this->relatedValue = $context[$this->field];
        return $this->relatedValue;
    }

    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        $relatedValue = $this->getRelatedValue($context);
        if ($relatedValue !== $value) {
            $this->error(self::NOT_EQUALS);
            return false;
        }

        return true;
    }
}
