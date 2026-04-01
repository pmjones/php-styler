<?php
declare(strict_types=1);

namespace PhpStyler\Token;

interface TSplittable
{
    public const int ATTRIBUTE = 10;

    public const int FOR_SEMICOLON = 20;

    public const int COMMA = 30;

    public const int FOR_COMMA = 40;

    public const int FN_DOUBLE_ARROW = 50;

    public const int TERNARY = 60;

    public const int COALESCE = 70;

    public const int BOOLEAN_OR = 80;

    public const int BOOLEAN_AND = 90;

    public const int COMPARISON = 100;

    public const int ADDITION = 110;

    public const int MULTIPLICATION = 120;

    public const int FLUENT = 130;
}
