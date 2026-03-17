<?php
declare(strict_types=1);

namespace PhpStyler\Format;

use PhpStyler\Rule;
use PhpStyler\Rule\LineRule;
use PhpStyler\Rule\TokenRule;
use PhpStyler\Style\StyleLocator;

class DefaultFormat implements Format
{
    public function eol() : string
    {
        return "\n";
    }

    public function lineLen() : int
    {
        return 88;
    }

    public function indentLen() : int
    {
        return 4;
    }

    public function indentTab() : bool
    {
        return false;
    }

    public function styles() : StyleLocator
    {
        return new StyleLocator();
    }

    /** @return array<TokenRule|LineRule> */
    public function rules() : array
    {
        return [
            new Rule\RemoveBom(),
            new Rule\ConvertListToArray(),
            new Rule\ConvertLongArrayToShort(),
            new Rule\ConvertElseIf(),
            new Rule\ExpandImports(),
            new Rule\RemoveUnusedImports(),
            new Rule\OrderImports(),
            new Rule\AddMissingVisibility(),
            new Rule\OrderModifiers(),
            new Rule\OrderTypes(),
            new Rule\NormalizeTrailingCommas(),
            new Rule\RemoveTrailingBlankLines(),
        ];
    }
}
