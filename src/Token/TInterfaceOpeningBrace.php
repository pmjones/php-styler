<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TInterfaceOpeningBrace extends TClasslikeOpeningBrace
{
    public const END_SEMICOLON = TPropertyEndSemicolon::class;
}
