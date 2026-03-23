<?php
declare(strict_types=1);

namespace PhpStyler\Format;

use PhpStyler\Rule;
use PhpStyler\Token;

class DeclarationFormat extends PlainFormat
{
    /**
     * @inheritdoc
     */
    public protected(set) array $parseAs = [
        Token\TList::class => Token\TListAsArray::class,
        Token\TArray::class => Token\TArrayAsShort::class,
        Token\TElse::class => Token\TElseAsElseIf::class,
        Token\TPhpClosingTag::class => Token\TPhpClosingTagRemoved::class,
        Token\TSemicolon::class => Token\TSemicolonSkipRepeats::class,
        Token\TVariable::class => Token\TVariableWithExplicitInterpolation::class,
    ];

    /**
     * @inheritdoc
     */
    public protected(set) array $rules = [
        // file-level cleanup
        Rule\RemoveBom::class => [],
        // structural
        Rule\AddControlBraces::class => [],
        // import cleanup
        Rule\RemoveUnusedImports::class => [],
        Rule\OrderImports::class => [],
        //
        // visibility and modifiers
        Rule\AddMissingVisibility::class => [],
        Rule\OrderModifiers::class => [],
        // types
        Rule\OrderTypes::class => [],
        // structural formatting
        Rule\MergeParenBracket::class => [],
        Rule\RejoinOrphans::class => [],
        Rule\NormalizeTrailingCommas::class => [],
        Rule\RemoveTrailingBlankLines::class => [],
    ];

    /**
     * @inheritdoc
     * @param array<class-string<\PhpStyler\Token\AToken>, class-string<\PhpStyler\Token\AToken>> $parseAs
     */
    public function __construct(
        string $eol = "\n",
        int $lineLen = 84,
        int $indentLen = 4,
        bool $indentTab = false,
        array $styles = [],
        array $rules = [],
        array $parseAs = [],
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
            parseAs: $parseAs,
        );
    }
}
