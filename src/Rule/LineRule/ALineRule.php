<?php
declare(strict_types=1);

namespace PhpStyler\Rule\LineRule;

use PhpStyler\Line;
use PhpStyler\Rule\ARule;

abstract class ALineRule extends ARule
{
    /**
     * @param Line[] $lines
     * @return Line[]
     */
    abstract public function apply(array $lines) : array;
}
