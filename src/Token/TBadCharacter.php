<?php
declare(strict_types=1);

namespace PhpStyler\Token;

/**
 * Token: T_BAD_CHARACTER
 *
 * Syntax: (n/a)
 *
 * Reference: anything below ASCII 32 except \t (0x09), \n (0x0a) and \r (0x0d)
 */
class TBadCharacter extends AToken
{
}
