<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TMagicMethod extends TFunction
{
    public const OPENING_BRACE = TFunctionOpeningBrace::class;

    public const CLOSING_BRACE = TMagicMethodClosingBrace::class;

    public const END_SEMICOLON = TAbstractMagicMethodEndSemicolon::class;
}
