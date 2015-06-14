<?php

namespace Application\Api\Controller;

use Flow;

use Application\Exception;

class ProductsController extends ApiController
{
    public function imageAction()
    {
        $table = $this->getTable();
        $id = $this->params()->fromRoute('id');
        try {
            $table->find($id);
        } catch (Exception\UnknowRegistryException $e) {
            $this->getResponse()->setStatusCode(404);
            return false;
        }
        $config = new Flow\Config();
        $applicationPath = __DIR__.'/../../../../../../';
        $config->setTempDir($applicationPath.'data/temp_images');
        $file = new Flow\File($config);

        if ($this->getRequest()->isGet()) {
            if (!$file->checkChunk()) {
                $this->getResponse()->setStatusCode(204);
                return false;
            }
        } else if ($this->getRequest()->isPost()) {
            if ($file->validateChunk()) {
                $file->saveChunk();
            } else {
                $this->getResponse()->setStatusCode(400);
                return false;
            }
        }
        $request = new Flow\Request();
        $parts = explode('.', $request->getFileName());
        $fileName = $id.'.data';
        if ($file->validateFile() && $file->save($applicationPath.'data/images/' . $fileName)) {
            $content = 'File saved!';
        } else {
            $content = 'Chunk created!';
        }
        return false;
    }

    public function getListConditions()
    {
        $result = [];

        $categoryId = $this->params()->fromQuery('category_id');
        if ($categoryId) {
            $result['category_id'] = $categoryId;
        }
        return $result;
    }
}
