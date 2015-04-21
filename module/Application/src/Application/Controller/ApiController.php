<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

use Application\Model;
use Application\Exception;

class ApiController extends AbstractRestfulController
{
    protected $table;

    public function __construct(Model\TableInterface $table, $singularName, $pluralName = null)
    {
        $this->setTable($table);
        $this->setNames($singularName, $pluralName);
    }

    public function setTable(Model\TableInterface $table)
    {
        $this->table = $table;
        return $this;
    }

    public function getTable()
    {
        return $this->table;
    }

    public function getList()
    {
        $perPage = (int)$this->params()->fromQuery('size', 100);
        if ($perPage <= 0) {
            $perPage = 100;
        }

        $sort = $this->params()->fromQuery('sort');
        $order = array();
        if (!is_null($sort)) {
            $aux = $this->params()->fromQuery('order');
            if (!is_null($aux)) {
                $order[$sort] = $aux;
            } else {
                $order[] = $sort;
            }
        }

        $paginated = ($this->params()->fromQuery('show_all', false) === false);

        $entries = $this->getTable()->fetchAll(
            NULL,
            array(
                'page' => (int)$this->params()->fromQuery('page', 1),
                'paginated' => $paginated,
                'perPage' => $perPage,
                'order' => $order,
            )
        );

        if (!$paginated) {
            return new JsonModel(array(
                $this->getPluralName() => $entries->toArray(),
                'page' => 1,
                'pages' => 1,
                'messages' => array(),
            ));
        } else {
            return new JsonModel(array(
                $this->getPluralName() => $entries->getCurrentItems(),
                'page' => $entries->getCurrentPageNumber(),
                'pages' => $entries->getPages()->pageCount,
                'messages' => array(),
            ));
        }
    }

    public function get($id) {
        $table = $this->getTable();
        $messages = array();

        try {
            $entry = $table->find($id);
        } catch (Exception\UnknowRegistryException $e) {
            $name = ucfirst($this->getSingularName());
            $this->getResponse()->setStatusCode(404);
            $messages[] = array('type' => 'error', 'code' => 'ERROR001', 'text' => "$name with identifier '$id' not exists!");
            $entry = array();
        }
        return new JsonModel(array($this->getSingularName() => $entry, 'messages' => $messages));

    }

    public function update($id, $data)
    {
        $table = $this->getTable();
        $result = array('messages' => array());
        $messages = &$result['messages'];
        $name = ucfirst($this->getSingularName());
        try {
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
                    $result['fields'][] = array('name' => $key, 'errors' => array_values($error));
                }
                $this->getResponse()->setStatusCode(400);
                $messages[] = array('type' => 'error', 'text' => 'Some fields are invalid');
            }
        } catch (Exception\UnknowRegistryException $e) {
            $this->getResponse()->setStatusCode(404);
            $messages[] = array('type' => 'error', 'code' => 'ERROR001', 'message' => "$name with identifier '$id' not exists!");
            $entry = array();
        }
        return new JsonModel($result);
    }

    public function create($data)
    {
        $table = $this->getTable();

        $result = array('messages' => array());
        $messages = &$result['messages'];

        $form = $table->getForm('create');

        $form->setData($data);
        if ($form->isValid()) {
            $result['id'] = $table->save(null, $form->getData());
            $name = ucfirst($this->getSingularName());
            $messages[] = array('type' => 'success', 'text' => "$name created successfully");
            $this->getResponse()->setStatusCode(201);
        } else {
            $this->getResponse()->setStatusCode(400);
            $errors = $form->getMessages();
            foreach ($errors as $key => $error) {
                $result['fields'][] = array('name' => $key, 'errors' => array_values($error));
            }
            $messages[] = array('type' => 'error', 'text' => 'Some fields are invalid');
        }
        return new JsonModel($result);
    }

    public function delete($id)
    {
        $table = $this->getTable();
        $messages = array();
        $name = ucfirst($this->getSingularName());
        try {
            $entry = $table->find($id);
            $table->delete($id);
            $messages[] = array('type' => 'success', 'text' => "$name removed successfully!");
        } catch (Exception\UnknowRegistryException $e) {
            $this->getResponse()->setStatusCode(404);
            $messages[] = array('type' => 'error', 'code' => 'ERROR001', 'text' => "$name with identifier '$id' not exists!");
            $entry = array();
        }

        return new JsonModel(array('messages' => $messages));
    }

    public function getSingularName() {
        return $this->singularName;
    }

    public function getPluralName() {
        return $this->pluralName;
    }

    protected function setNames($singularName, $pluralName = null) {
        $this->singularName = strtolower((string)$singularName);
        if (is_null($pluralName)) {
            $pluralName = $singularName.'s';
        }
        $this->pluralName = strtolower($pluralName);
    }
}
