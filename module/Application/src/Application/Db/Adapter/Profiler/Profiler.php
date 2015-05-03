<?php

namespace Application\Db\Adapter\Profiler;

class Profiler extends \Zend\Db\Adapter\Profiler\Profiler
{

    protected $logger;

    public function __construct(\Zend\Log\LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function profilerFinish()
    {
        parent::profilerFinish();
        $profile = current($this->profiles);
        $data = array();
        foreach ((array)$profile['parameters']->getNamedArray() as $key => $value) {
            $data[] = "$key = $value";
        }
        $msg = 'Sql: ' . $profile['sql'];
        $msg .= ' | Data: '.implode(',', $data);
        $msg .= ' | Time: '. $profile['elapse'];
        $this->logger->debug($msg);

        $this->profiles = array();
        $this->currentIndex = 0;
    }
}
