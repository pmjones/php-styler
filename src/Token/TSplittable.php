<?php
declare(strict_types=1);

namespace PhpStyler\Token;

interface TSplittable
{
    public const int ATTRIBUTE = 5;

    public const int FN_DOUBLE_ARROW = 10;

    public const int COMMA = 20;

    public const int LOOSE_OPERATOR = 30;

    public const int TIGHT_OPERATOR = 40;

    public const int FLUENT = 50;

    public const int FOR_SEMICOLON = 60;
}
