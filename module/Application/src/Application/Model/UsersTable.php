<?php

namespace Application\Model;

use Application\Form;
use Application\Exception;

use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;

class UsersTable extends AbstractTable
{

    protected function loadForm($identifier)
    {
        $form = null;
        switch ($identifier) {
            case 'login':
                $form = new Form\LoginForm();
                break;
            case 'create':
                $form = new Form\UserForm();
                $form->setInputFilter(new Form\UserFilter($this->getServiceLocator()));
                break;
            case 'edit':
                $form = new Form\UserForm();
                $form->setInputFilter(new Form\UserEditFilter($this->getServiceLocator()));
                break;
        }
        return $form;
    }

    public function find($id, array $additionalWhere = [])
    {
        $hash = null;
        if (array_key_exists('hash', $additionalWhere)) {
            $hash = $additionalWhere['hash'];
            unset($additionalWhere['hash']);
        }

        $user = parent::find($id, $additionalWhere);
        if (!is_null($hash) && $this->generateHash($user) != $hash) {
            throw new Exception\UnknowRegistryException();
        }
        return $user;
    }

    protected function getBaseSelect($where, $options)
    {
        $select = parent::getBaseSelect($where, $options);
        $select->join(['g' => 'groups'], 'g.id = users.group_id', ['group_name' => 'name']);
        return $select;
    }

    protected function filterData($user)
    {
        unset($user['password']);
        $model = $this->getServiceLocator()->get('Application\Model\GroupsTable');
        $user['group'] = $model->find($user['group_id']);
        return $user;
    }

    public function save($id, $data)
    {
        if (isset($data['password'])) {
            $data['password'] = sha1($data['password']);
        }

        $result = parent::save($id, $data);
        if (is_null($id) && $result) {
            $this->sendWelcomeMail($result);
        }
        return $result;
    }

    public function generateHash($user)
    {
        $data = sha1($user['email'].$user['id'].$user['name'].'FORCADEVENDAS');
        return $data;
    }

    public function authenticate($params)
    {
        $sm = $this->getServiceLocator();
        $auth = $sm->get('Application\Auth');

        $authAdapter = new AuthAdapter($params['email'], $params['password'], $this);
        return $auth->authenticate($authAdapter);
    }

    public function sendWelcomeMail($id)
    {
        $user = $this->find($id);

        // Setup SMTP transport
        $transport = new SmtpTransport();
        $options   = new SmtpOptions([
            'name' => 'gmail',
            'host' => 'smtp.gmail.com',
            'port' => 587,
            'connection_class' => 'plain',
            'connection_config' => [
                'username' => 'luizteste720@gmail.com',
                'password' => 'batata%asd',
                'ssl' => 'tls',
            ]
        ]);
        $transport->setOptions($options);
        $message = new Message();
        $message
            ->addFrom('felipe.silvacunha@gmail.com', 'Força de Vendas')
            ->addTo($user['email'], $user['name'])
            ->setSubject('Bem Vindo(a) ao Força de Vendas');

        $html = new MimePart($this->getWelcomeHtml($user, $this->generateHash($user)));
        $html->type = "text/html";

        $body = new MimeMessage();
        $body->setParts([ $html ]);
        $message->setBody($body);

        $transport->send($message);
    }

    protected function getWelcomeHtml($user, $hash)
    {
        $config = $this->getServiceLocator()->get('config');
        $url = $config['external_url'].'/api-change-password/';
        $name = $user['name'];
        $id = $user['id'];
        $html = <<<HTML
<html>
    <body>
        <strong>Olá $name,</strong><br/>
        <p>Seja bem-vindo(a) ao sistema de força de vendas!</p>
        <p>Para começar a utilizar o sistema é necessário acessar <a href="$url/#/users/$id/welcome/$hash">$url/#/users/$id/welcome/$hash</a> e definir a sua senha de acesso.</p>
        <br/><br/>
        <strong>Atenciosamente,</strong>
        <br/><br/>
        Setor de vendas
    </body>
</html>
HTML;
        return $html;
    }
}
