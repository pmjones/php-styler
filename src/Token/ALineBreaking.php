<?php
declare(strict_types=1);

namespace PhpStyler\Token;

/**
 * Marker for tokens that represent a break in the output stream: TLineBreak,
 * TWhitespaceEol, TBlankLine. Lets callers ask "is there a line break (in any
 * form) here?" without enumerating the three specific classes.
 */
interface ALineBreaking
{
}
