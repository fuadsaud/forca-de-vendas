<?php

namespace Application\Api\Controller;

use Zend\View\Model\JsonModel;

class OrdersController extends ApiController
{
    public function mensalAction()
    {
        $data = $this->getTable()->relatorioMensal();
        return new JsonModel($data);
    }

    public function anualAction()
    {
        $data = $this->getTable()->relatorioAnual();
        return new JsonModel($data);
    }
}
