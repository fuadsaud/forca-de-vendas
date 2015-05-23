<?php

namespace Application\Validator;

use Application\Model;
use Application\Exception;

use Zend\Validator\AbstractValidator;

class Exists extends AbstractValidator
{
    const NOT_EXISTS = 'not_exists';

    protected $messageTemplates = [
        self::NOT_EXISTS => 'Entry does not exists',
    ];

    protected $table;

    public function __construct(Model\TableInterface $table)
    {
        $this->setTable($table);
    }

    public function setTable(Model\TableInterface $table)
    {
        $this->table = $table;

        return $this;
    }


    public function isValid($value)
    {
        $result = false;
        try {
            if ($this->table->find($value)) {
                $result = true;
            }
        } catch (Exception\UnknowRegistryException $e) {
            $result = false;
        }

        return $result;
    }

}
