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
    public protected(set) array $parseAs = [
        Token\TElse::class => Token\TElseAsElseIf::class,
        Token\TVariable::class => Token\TVariableWithExplicitInterpolation::class,
    ];

    /**
     * @inheritdoc
     */
    public protected(set) array $rules = [
        TokenRule\RemoveBom::class => [],
        TokenRule\RemoveEmptyAnonymousClassParens::class => [],
        TokenRule\RemoveEmptyAttributeParens::class => [],
        TokenRule\InjectNewParens::class => [],
        TokenRule\RemoveLanguageConstructParens::class => [],
        TokenRule\ExpandGroupedImports::class => [],
        TokenRule\ExpandPropertyDeclarations::class => [],
        TokenRule\ExpandConstDeclarations::class => [],
        TokenRule\ConvertVarToPublic::class => [],
        TokenRule\InsertPublicVisibility::class => [],
        TokenRule\ReorderModifiers::class => [],
        TokenRule\ConvertToShortArraySyntax::class => [],
        TokenRule\ConvertToShortListSyntax::class => [],
        TokenRule\RemovePhpClosingTag::class => [],
        TokenRule\RemoveRepeatedSemicolons::class => [],
        TokenRule\NormalizeImports::class => [],
        TokenRule\OrderTypes::class => [],
        TokenRule\MergeParenBracket::class => [],
        LineRule\RejoinOrphans::class => [],
        LineRule\NormalizeTrailingCommas::class => [],
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
