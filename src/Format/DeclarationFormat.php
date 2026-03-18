<?php
declare(strict_types=1);

namespace PhpStyler\Format;

use PhpStyler\Rule;

class DeclarationFormat extends PlainFormat
{
    /**
     * @inheritdoc
     */
    public protected(set) array $rules = [
        Rule\RemoveBom::class => [],
        Rule\ConvertListToArray::class => [],
        Rule\ConvertLongArrayToShort::class => [],
        Rule\ConvertElseIf::class => [],
        Rule\AddControlBraces::class => [],
        Rule\ExpandImports::class => [],
        Rule\RemoveUnusedImports::class => [],
        Rule\OrderImports::class => [],
        Rule\AddMissingVisibility::class => [],
        Rule\OrderModifiers::class => [],
        Rule\OrderTypes::class => [],
        Rule\MergeParenBracket::class => [],
        Rule\NormalizeTrailingCommas::class => [],
        Rule\RemoveTrailingBlankLines::class => [],
    ];

    /**
     * @inheritdoc
     */
    public function __construct(
        string $eol = "\n",
        int $lineLen = 88,
        int $indentLen = 4,
        bool $indentTab = false,
        array $styles = [],
        array $rules = [],
    ) {
        parent::__construct(
            eol: $eol,
            lineLen: $lineLen,
            indentLen: $indentLen,
            indentTab: $indentTab,
            classBracePosition: 'next_line',
            functionBracePosition: 'next_line',
            controlBracePosition: 'same_line',
            keywordCase: 'lower',
            concatenationSpacing: true,
            returnTypeColonSpacing: true,
            blankLineAfterBlock: true,
            styles: $styles,
            rules: $rules,
        );
    }
}
