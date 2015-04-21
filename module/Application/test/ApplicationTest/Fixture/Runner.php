<?php

namespace ApplicationTest\Fixture;

class Runner
{

    use \Zend\ServiceManager\ServiceLocatorAwareTrait;

    public function uses(array $objects)
    {
        $obs = array();
        foreach ($objects as $object) {
            if (!is_object($object)) {
                $object = (string)$object;
                if ($this->getServiceLocator()->has($object)) {
                    $object = $this->getServiceLocator()->get($object);
                } else {
                    $filter = new \Zend\Filter\Word\UnderscoreToCamelCase();
                    $name = 'ApplicationTest\Fixture\\' . $filter->filter(strtolower($object));
                    if ($this->getServiceLocator()->has($name)) {
                        $object = $this->getServiceLocator()->get($name);
                    } else {
                        throw new \Exception('Invalid object for '. $name);
                    }
                }
            }
            if (!$object instanceof FixtureInterface) {
                throw new \Exception(get_class($object).' is not a valid Fixture!');
            }
            $obs[] = $object;
        }
        for ($i=count($obs)-1; $i>=0; $i--) {
            $obs[$i]->clean();
        }

        foreach ($obs as $object) {
            $object->init();
        }
    }
}
