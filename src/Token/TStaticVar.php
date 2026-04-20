<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TStaticVar extends AToken implements AStatementNesting
{
    public const END_SEMICOLON = TStaticVarEndSemicolon::class;
}
