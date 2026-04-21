<?php
declare(strict_types=1);

namespace PhpStyler\Token;

interface ASplittable
{
    public const CONDITION_PAREN = 10;

    public const ATTRIBUTE = 20;

    public const FOR_SEMICOLON = 30;

    public const COMMA = 40;

    public const FOR_COMMA = 50;

    public const FN_ARROW = 60;

    public const TERNARY = 70;

    public const COALESCE = 80;

    public const BRACKET = 90;

    public const MATCH_ARROW = 100;

    public const BOOLEAN_OR = 110;

    public const BOOLEAN_AND = 120;

    public const BITWISE_OR = 122;

    public const BITWISE_XOR = 124;

    public const BITWISE_AND = 126;

    public const COMPARISON = 130;

    public const ADDITION = 140;

    public const MULTIPLICATION = 150;

    public const FLUENT = 160;

    public const OTHER_PAREN = 170;

    public const ELEMENT_BRACKET = 180;
}
