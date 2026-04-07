<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TUseFunction extends AToken
{
    public const OPENING_BRACE = TUseFunctionOpeningBrace::class;

    public const CLOSING_BRACE = TUseFunctionClosingBrace::class;

    public const END_SEMICOLON = TUseEndSemicolon::class;
}
