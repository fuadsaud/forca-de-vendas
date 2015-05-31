<?php

namespace Application\Api\Controller;

class AddressesController extends ApiController
{

    public function getListConditions()
    {
        return ['client_id' => $this->params()->fromRoute('client_id')];
    }

    public function getFindConditions()
    {
        return ['client_id' => $this->params()->fromRoute('client_id')];
    }

    public function delete($id)
    {
        $this->getRespons()->setStatus(405);
        return ['content' => 'method not allowed'];
    }

    public function create($data)
    {
        $this->getRespons()->setStatus(405);
        return ['content' => 'method not allowed'];
    }
}
