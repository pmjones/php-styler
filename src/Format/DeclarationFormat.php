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
        // file-level cleanup
        Rule\RemoveBom::class => [],
        Rule\RemovePhpClosingTag::class => [],
        // syntax normalization
        Rule\ConvertListToArray::class => [],
        Rule\ConvertLongArrayToShort::class => [],
        Rule\ConvertElseIf::class => [],
        Rule\ConvertHeredocToNowdoc::class => [],
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
        Rule\NormalizeTrailingCommas::class => [],
        Rule\RemoveTrailingBlankLines::class => [],
        // paren normalization
        Rule\AddInstantiationParentheses::class => [],
        Rule\AddExitParentheses::class => [],
        Rule\RemoveEmptyAnonymousClassParens::class => [],
        Rule\RemoveEmptyAttributeParens::class => [],
        // string and cleanup
        Rule\ConvertImplicitInterpolation::class => [],
        Rule\RemoveRepeatedSemicolons::class => [],
        Rule\RemoveParensFromLanguageConstructs::class => [],
        // spacing (must be last — depends on expanded members)
        Rule\NormalizeMemberSpacing::class => [],
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
