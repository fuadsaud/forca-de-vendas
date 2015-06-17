<?php

namespace Application\Model;

use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Role\GenericRole as Role;
use Zend\Permissions\Acl\Resource\GenericResource as Resource;

/**
 * Model to manage system permissions
 *
 * @category Application
 * @package Application\Model
 */
class Permissions
{

    /**
     * Service Locator
     *
     * @var mixed
     * @access protected
     */
    protected $service;

    /**
     * Access control list
     *
     * @var \Zend\Permissions\Acl\Acl;
     * @access protected
     */
    protected $acl;

    /**
     * User identity
     *
     * @var mixed
     * @access protected
     */
    protected $identity;

    /**
     * Class contructor
     *
     * @param mixed $service
     * @param mixed $identity
     * @access public
     */
    public function __construct($service, $identity)
    {
        $this->setService($service)
            ->setIdentity($identity)
            ->init();
    }

    /**
     * Define user identity
     *
     * @param mixed $identity
     * @access public
     * @return Permissions
     */
    public function setIdentity($identity)
    {
        $this->identity = $identity;
        return $this;
    }

    /**
     * Get identity
     *
     * @access public
     * @return mixed
     */
    public function getIdentity() {
        return $this->identity;
    }

    /**
     * Define Service locator
     *
     * @param mixed $service
     * @access public
     * @return Permissions
     */
    public function setService($service)
    {
        $this->service = $service;
        return $this;
    }

    /**
     * Get acl object
     *
     * @access public
     * @return \Zend\Permission\Acl\Acl
     */
    public function getAcl()
    {
        return $this->acl;
    }

    /**
     * Initialize permissions
     *
     * @access protected
     * @return Permissions
     */
    protected function init()
    {
        $acl = new Acl();
        $this->resources($acl)
            ->adminRole($acl)
            ->representanteRole($acl);

        $this->acl = $acl;

        return $this;
    }

    /**
     * Create resource
     *
     * @param \Zend\Permissions\Acl\Acl $acl
     * @access protected
     * @return Permissions
    */
    protected function resources($acl)
    {
        $acl->addResource(new Resource('clients'));
        $acl->addResource(new Resource('products'));
        $acl->addResource(new Resource('users'));
        $acl->addResource(new Resource('categories'));
        $acl->addResource(new Resource('payments'));
        $acl->addResource(new Resource('reports'));
        return $this;
    }

    /**
     * Create admin role
     *
     * @param \Zend\Permissions\Acl\Acl $acl
     * @access protected
     * @return Permissions
    */
    protected function adminRole($acl)
    {
        $acl->addRole(new Role('admin'));
        $acl->allow('admin');
        return $this;
    }

    /**
     * Create representante role
     *
     * @param \Zend\Permissions\Acl\Acl $acl
     * @access protected
     * @return Permissions
    */
    protected function representanteRole($acl)
    {
        $representante = new Role('representante');
        $acl->addRole($representante);

        $acl->allow($representante, 'users', array('retrieve_list', 'retrieve'));
        $acl->allow($representante, 'products', array('retrieve_list', 'retrieve'));
        $acl->allow($representante, 'categories', array('retrieve_list', 'retrieve'));
        $acl->allow($representante, 'clients', array('create', 'retrieve_list', 'retrieve', 'update'));
        $acl->allow($representante, 'payments', array('retrieve_list', 'retrieve'));
        return $this;
    }

    /**
     * Verify if user is allowed
     *
     * @param string $identifier
     * @param string $method
     * @access public
     * @return boolean
     */
    public function isAllowed($identifier, $method)
    {
        $groups = array();
        $identity = $this->getIdentity();
        if (isset($identity['group'])) {
            $groups = [$identity['group']];
        }
        foreach ($groups as $group) {
            if ($this->getAcl()->isAllowed($group['name'], $identifier, $method)) {
                return true;
            }
        }
        return false;
    }
}
