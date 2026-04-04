<?php
declare(strict_types=1);

namespace PhpStyler\Token;

/**
 * Token: T_CONSTANT_ENCAPSED_STRING
 *
 * Syntax: "foo" or &#039;bar&#039;
 *
 * Reference: https://www.php.net/manual/en/language.types.string.php#language.types.string.syntax string syntax
 */
class TStringLiteral extends AToken implements ALiteral
{
}
