<?php

namespace Application\Model;
use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;


class AuthAdapter implements AdapterInterface
{

    protected $username;
    protected $password;
    protected $table;

    public function __construct($username, $password, $table)
    {
        $this->username = $username;
        $this->password = sha1($password);
        $this->table = $table;
    }

    /**
     * Performs an authentication attempt
     *
     * @return \Zend\Authentication\Result
     * @throws \Zend\Authentication\Adapter\Exception\ExceptionInterface
     *               If authentication cannot be performed
     */
    public function authenticate()
    {
        /* $user = $this->table->fetchAll([ */
        /*     'email' => $this->username, */
        /*     'password' => $this->password */
        /* ])->current(); */

        $user = $this->table->fetchAll([])->current();


        if ($user) {
            return new Result(Result::SUCCESS, $user->getArrayCopy());
        } else {
            return new Result(Result::FAILURE, null, ['Usuário e/ou senha inválidos']);
        }
    }
}
