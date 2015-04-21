<?php

namespace ApplicationTest\Fixture;

interface FixtureInterface
{
    public function init();
    public function clean();
}
