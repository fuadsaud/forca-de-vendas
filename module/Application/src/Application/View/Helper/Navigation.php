<?php

namespace Application\View\Helper;

use Zend\View\Helper\Navigation as ZendNavigation;

/**
 * Navigation Helper with acl included
 *
 * @uses ZendNavigation
 */
class Navigation extends ZendNavigation
{

    /**
     * Constructor
     *
     * @param mixed $permissions
     * @access public
     */
    public function __construct($permissions)
    {
        $this->setPermissions($permissions)->setUseAcl(true);
    }

    /**
     * Define permissions
     *
     * @param mixed $permissions
     * @access public
     * @return Navigation
     */
    public function setPermissions($permissions)
    {
        $this->permissions = $permissions;
        return $this;
    }

    /**
     * Get Permissions
     *
     * @access public
     * @return mixed
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * Verify if navigation is allowed
     *
     * @param array $params
     * @access public
     * @return boolean
     */
    public function isAllowed($params)
    {
        $pagePermissions = $params['page']->get('permissions');
        $plugin = $this->getPermissions();
        $result = true;
        if (is_array($pagePermissions)) {
            $result = $plugin->isAllowed($pagePermissions['identifier'], $pagePermissions['method']);
        }
        return $result;
    }

}
