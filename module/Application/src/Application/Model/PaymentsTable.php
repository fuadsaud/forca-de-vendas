<?php

namespace Application\Model;

use Application\Form;
use Application\Exception;

class PaymentsTable extends AbstractTable
{
    public function loadForm($identifier)
    {
        $form = new Form\PaymentForm();
        $form->setInputFilter(new Form\PaymentFilter());
        return $form;
    }

    public function save($id, $data)
    {
        $forms = null;

        if (array_key_exists('forms', $data)) {
            $forms = $data['forms'];
            unset($data['forms']);
        }

        try {
            $this->beginTransaction();
            $id = parent::save($id, $data);
            if (!is_null($forms)) {
                $this->syncForms($id, $forms);
            }
            $this->commit();
        } catch (\Exception $e)  {
            $this->rollback();
            if ($e instanceof Exception\UnknowRegistryException) {
                throw $e;
            }
            throw new Exception\RuntimeException($e->getMessage());
        }
        return $id;
    }

    protected function syncForms($id, $data)
    {
        $table = $this->getServiceLocator()->get('Application\Model\PaymentsFormsTable');
        $table->delete(['payment_id' => $id]);
        foreach ($data as $form) {
            $form['payment_id'] = $id;
            $table->save(null, $form);
        }
    }

    protected function filterData($payment)
    {
        $table = $this->getServiceLocator()->get('Application\Model\PaymentsFormsTable');
        $payment['forms'] = $table->fetchAll(['payment_id' => $payment['id']], ['paginated' => false])->toArray();
        return $payment;
    }
}
