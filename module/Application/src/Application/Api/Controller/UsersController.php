<?php

namespace Application\Api\Controller;

class UsersController extends ApiController
{
    public function getFindConditions()
    {
        $result = [];

        $hash = $this->params()->fromQuery('hash');
        if ($hash) {
            $result['hash'] = $hash;
        }
        return $result;
    }
}
