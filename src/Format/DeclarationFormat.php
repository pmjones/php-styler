<?php
declare(strict_types=1);

namespace PhpStyler\Format;

use PhpStyler\Rule\LineRule;
use PhpStyler\Rule\TokenRule;
use PhpStyler\Token;

class DeclarationFormat extends PlainFormat
{
    /**
     * @inheritdoc
     */
    public array $parseAs = [
        Token\TElse::class => Token\TElseAsElseIf::class,
        Token\TVariable::class => Token\TVariableWithExplicitInterpolation::class,
    ];

    /**
     * @inheritdoc
     */
    public array $rules = [
        TokenRule\RemoveBom::class => [],
        TokenRule\RemoveEmptyAnonymousClassParens::class => [],
        TokenRule\RemoveEmptyAttributeParens::class => [],
        TokenRule\InsertNewParens::class => [],
        TokenRule\RemoveLanguageConstructParens::class => [],
        TokenRule\ExpandGroupedImports::class => [],
        TokenRule\ExpandPropertyDeclarations::class => [],
        TokenRule\ExpandConstDeclarations::class => [],
        TokenRule\ConvertVarToPublic::class => [],
        TokenRule\InsertPublicVisibility::class => [],
        TokenRule\NormalizeModifierOrder::class => [],
        TokenRule\ConvertToShortArraySyntax::class => [],
        TokenRule\ConvertToShortListSyntax::class => [],
        TokenRule\RemovePhpClosingTag::class => [],
        TokenRule\RemoveRepeatedSemicolons::class => [],
        TokenRule\NormalizeImports::class => [],
        TokenRule\NormalizeTypeOrder::class => [],
        TokenRule\MergeParenBracket::class => [],
        LineRule\MergeParenBrace::class => [],
        LineRule\NormalizeTrailingCommas::class => [],
        LineRule\NormalizeMemberOrder::class => [],
        LineRule\RemoveTrailingBlankLines::class => [],
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
