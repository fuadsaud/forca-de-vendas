<?php

namespace Application\Api\Controller;

use Zend\View\Model\JsonModel;

use Application\Exception;

class UsersController extends ApiController
{
    public function getFindConditions()
    {
        $result = [];

        return $result;
    }

    public function changePasswordAction()
    {
        $hash = $this->params()->fromRoute('hash');
        $id = $this->params()->fromRoute('id');

        $table = $this->getTable();
        $result = array('messages' => array());
        $messages = &$result['messages'];
        try {
            $user = $table->find($id, ['hash' => $hash]);
            if ($this->getRequest()->isPut()) {
                $data = $this->processBodyContent($this->getRequest());
                $name = ucfirst($this->getSingularName());

                $form = $table->getForm('edit');
                $form->setData((array)$data);
                if ($form->isValid()) {
                    $table->save($id, $form->getData());
                    $messages[] = array('type' => 'success', 'text' => "$name edited successfully");
                    $this->getResponse()->setStatusCode(200);
                    $result['id'] = $id;
                } else {
                    $errors = $form->getMessages();
                    foreach ($errors as $key => $error) {
                        $result['fields'][] = array('name' => $key, 'errors' => $error);
                    }
                    $this->getResponse()->setStatusCode(400);
                    $messages[] = array('type' => 'error', 'text' => 'Some fields are invalid');
                }
            } else {
                $result = array($this->getSingularName() => $user);
            }
        } catch (Exception\UnknowRegistryException $e) {
            $name = ucfirst($this->getSingularName());
            $this->getResponse()->setStatusCode(404);
            $messages[] = array('type' => 'error', 'code' => 'ERROR001', 'text' => "$name with identifier '$id' not exists!");
            $entry = array();
        }

        return new JsonModel($result);
    }
}
