<?php
declare(strict_types=1);

namespace PhpStyler\Token;

/**
 * Token: T_NULLSAFE_OBJECT_OPERATOR
 *
 * Syntax: ?->
 *
 * Reference: https://www.php.net/manual/en/language.oop5.php classes and objects
 */
class TNullsafeObjectOperator extends AnObjectOperator
{
    protected const ENCAPSED_CLASS = TEncapsedNullsafeObjectOperator::class;
}
