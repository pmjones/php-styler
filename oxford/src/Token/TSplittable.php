<?php
declare(strict_types=1);

namespace PhpStyler\Token;

interface TSplittable
{
    const int ATTRIBUTE = 5;

    const int FN_DOUBLE_ARROW = 10;

    const int COMMA = 20;

    const int LOOSE_OPERATOR = 30;

    const int TIGHT_OPERATOR = 40;

    const int FLUENT = 50;

    const int FOR_SEMICOLON = 60;

    public function splitCategory() : int;
}
