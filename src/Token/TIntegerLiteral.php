<?php
declare(strict_types=1);

namespace PhpStyler\Token;

/**
 * Token: T_LNUMBER
 *
 * Syntax: 123, 012, 0x1ac, etc.
 *
 * Reference: https://www.php.net/manual/en/language.types.integer.php integers
 */
class TIntegerLiteral extends AToken implements ALiteral
{
}
