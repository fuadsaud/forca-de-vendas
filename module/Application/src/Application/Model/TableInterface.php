<?php

namespace Application\Model;

interface TableInterface
{
    public function find($id);
    public function fetchAll($where, $options);
    public function save($id, $data);
    public function delete($where);
}
