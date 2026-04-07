<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TUseConst extends AToken
{
    public const OPENING_BRACE = TUseConstOpeningBrace::class;

    public const CLOSING_BRACE = TUseConstClosingBrace::class;

    public const END_SEMICOLON = TUseEndSemicolon::class;
}
