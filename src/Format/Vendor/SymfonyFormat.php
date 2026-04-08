<?php
declare(strict_types=1);

namespace PhpStyler\Format\Vendor;

use PhpStyler\Format\DeclarationFormat;
use PhpStyler\Rule\LineRule;
use PhpStyler\Rule\TokenRule;
use PhpStyler\Token;

class SymfonyFormat extends DeclarationFormat
{
    /**
     * @inheritdoc
     */
    public array $parseAs = [
        Token\TList::class => Token\TListAsArray::class,
        Token\TArray::class => Token\TArrayAsShort::class,
        Token\TElse::class => Token\TElseAsElseIf::class,
        Token\TStringLiteral::class => Token\TStringLiteralAsSingleQuote::class,
        Token\TSemicolon::class => Token\TSemicolonSkipRepeats::class,
        Token\TContinue::class => Token\TContinueAsBreak::class,
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
        TokenRule\NormalizeImports::class => [],
        TokenRule\NormalizeTypeOrder::class => [],
        TokenRule\MergeParenBracket::class => [],
        TokenRule\ConvertToYodaConditions::class => [],
        LineRule\NormalizeMemberSpacing::class => [],
        LineRule\MergeParenBrace::class => [],
        LineRule\NormalizeTrailingCommas::class => [],
        LineRule\RemoveTrailingBlankLines::class => [],
    ];

    /**
     * @inheritdoc
     */
    public function __construct(
        string $eol = "\n",
        int $lineLen = 120,
        int $indentLen = 4,
        bool $indentTab = false,
        array $styles = [],
        array $rules = [],
    ) {
        $symfonyStyles = [Token\TReturn::class => ['blankLineBefore' => true]];

        foreach ($styles as $class => $args) {
            $symfonyStyles[$class] = array_merge(
                $symfonyStyles[$class] ?? [],
                $args,
            );
        }

        parent::__construct(
            eol: $eol,
            lineLen: $lineLen,
            indentLen: $indentLen,
            indentTab: $indentTab,
            styles: $symfonyStyles,
            rules: $rules,
        );

        $this->setConcatenationSpacing(false);
        $this->setReturnTypeColonSpacing(false);
    }
}
