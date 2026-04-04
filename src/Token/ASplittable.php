<?php
declare(strict_types=1);

namespace PhpStyler\Token;

interface ASplittable
{
    public const int CONDITION_PAREN = 10;

    public const int ATTRIBUTE = 20;

    public const int FOR_SEMICOLON = 30;

    public const int COMMA = 40;

    public const int FOR_COMMA = 50;

    public const int FN_ARROW = 60;

    public const int TERNARY = 70;

    public const int COALESCE = 80;

    public const int BRACKET = 90;

    public const int MATCH_ARROW = 100;

    public const int BOOLEAN_OR = 110;

    public const int BOOLEAN_AND = 120;

    public const int COMPARISON = 130;

    public const int ADDITION = 140;

    public const int MULTIPLICATION = 150;

    public const int FLUENT = 160;

    public const int OTHER_PAREN = 170;

    public const int ELEMENT_BRACKET = 180;
}
