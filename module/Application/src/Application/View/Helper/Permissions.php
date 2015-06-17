<?php
namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;

class Permissions extends AbstractHelper
{
    public function __construct($permissionsPlugin)
    {
        $this->setPermissionsPlugin($permissionsPlugin);
    }

    public function __invoke()
    {
        return $this;
    }

    public function setPermissionsPlugin($permissionsPlugin)
    {
        $this->permissionsPlugin = $permissionsPlugin;
        return $this;
    }

    public function getPermissionsPlugin()
    {
        return $this->permissionsPlugin;
    }

    public function __call($method, $args)
    {
        return call_user_func_array(array($this->getPermissionsPlugin(), $method), $args);
    }
}
