<?php

namespace ApplicationTest\Assert;

trait ArrayCompareTrait
{
    public function assertArrayInto(array $base, array $array, $delta = 0.0, $maxDepth = 10, $canonicalize = false, $ignoreCase = false)
    {
        foreach ($base as $key => $value) {

            $constraint = new \PHPUnit_Framework_Constraint_ArrayHasKey($key);
            $constraint->evaluate($array, '');

            $constraint = new \PHPUnit_Framework_Constraint_IsEqual(
                $value,
                $delta,
                $maxDepth,
                $canonicalize,
                $ignoreCase
            );

            $constraint->evaluate($array[$key], '');
        }
    }
}
