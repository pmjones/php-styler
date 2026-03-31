<?php
declare(strict_types=1);

namespace PhpStyler\Token;

interface TSplittable
{
    public const int ATTRIBUTE = 10;

    public const int COMMA = 20;

    public const int FN_DOUBLE_ARROW = 30;

    public const int TERNARY = 40;

    public const int COALESCE = 50;

    public const int BOOLEAN_OR = 60;

    public const int BOOLEAN_AND = 70;

    public const int ADDITION = 80;

    public const int MULTIPLICATION = 90;

    public const int FLUENT = 100;

    public const int FOR_SEMICOLON = 110;
}
