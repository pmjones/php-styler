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
    public protected(set) array $parses = [
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
        // expansion
        Rule\ExpandImports::class => [],
        Rule\ExpandTraitUse::class => [],
        Rule\ExpandConstants::class => [],
        Rule\ExpandProperties::class => [],
        // import cleanup (after expansion)
        Rule\RemoveImportLeadingBackslash::class => [],
        Rule\RemoveUnusedImports::class => [],
        Rule\OrderImports::class => [],
        // attributes
        Rule\ExpandAttributes::class => [],
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
        // paren normalization
        Rule\AddInstantiationParentheses::class => [],
        Rule\AddExitParentheses::class => [],
        Rule\RemoveEmptyAnonymousClassParens::class => [],
        Rule\RemoveEmptyAttributeParens::class => [],
    ];

    /**
     * @inheritdoc
     * @param array<class-string<\PhpStyler\Token\AToken>, class-string<\PhpStyler\Token\AToken>> $parses
     */
    public function __construct(
        string $eol = "\n",
        int $lineLen = 84,
        int $indentLen = 4,
        bool $indentTab = false,
        array $styles = [],
        array $rules = [],
        array $parses = [],
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
            parses: $parses,
        );
    }
}
