<?php

namespace Application\Validator;

use Zend\Validator\AbstractValidator;
use Application\Model\TableInterface;

class UniqueEntry extends AbstractValidator
{

    const MSG_NOT_UNIQUE = 'msgNotUnique';

    protected $messageTemplates = array(
        self::MSG_NOT_UNIQUE => '%values% are already exists, and cannot be duplicated'
    );

    protected $values = '';

    protected $messageVariables = array('values' => 'values');

    protected $table;

    protected $fields;

    protected $ignoreFields = array();

    public function setTable(TableInterface $table)
    {
        $this->table = $table;
        return $this;
    }

    public function setFields(array $fields)
    {
        $this->fields = $fields;
        return $this;
    }

    public function getTable()
    {
        return $this->table;
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function setIgnoreFields(array $fields)
    {
        $this->ignoreFields = $fields;
        return $this;
    }

    public function getIgnoreFields()
    {
        return $this->ignoreFields;
    }

    public function isValid($value, array $context = null)
    {
        $table = $this->getTable();
        if (is_null($table)) {
            throw new \RuntimeException('Table must be seted!');
        }

        $fields = $this->getFields();
        if (empty($fields)) {
            throw new \RuntimeException('Fields must be seted!');
        }


        $data = array();
        foreach ($fields as $field) {
            if (!array_key_exists($field,$context)) {
                return false;
            }
            $data[$field] = $context[$field];
        }
        $ignoreFields = $this->getIgnoreFields();
        foreach ($ignoreFields as $tableField => $formField) {
            if (is_numeric($tableField)) {
                $tableField = $formField;
            }
            if (array_key_exists($formField, $context) && !empty($context[$formField])) {
                $data[] = "$tableField != {$context[$formField]}";
            }
        }

        $info = current($table->fetchAll($data, array('paginated' => false))->toArray());
        if ($info) {
            $this->values = $value;
            $this->error(self::MSG_NOT_UNIQUE);
            return false;
        }

        return true;
    }
}
