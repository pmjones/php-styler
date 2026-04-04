<?php
declare(strict_types=1);

namespace PhpStyler\Rule\TokenRule;

use PhpStyler\Rule\ARule;
use PhpStyler\Token\AToken;

abstract class ATokenRule extends ARule
{
    /**
     * @param AToken[] $tokens
     * @return AToken[]
     */
    abstract public function apply(array $tokens) : array;
}
