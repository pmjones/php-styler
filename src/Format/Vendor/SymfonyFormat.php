<?php
declare(strict_types=1);

namespace PhpStyler\Format\Vendor;

use PhpStyler\Format\DeclarationFormat;
use PhpStyler\Rule;
use PhpStyler\Token;

class SymfonyFormat extends DeclarationFormat
{
    /**
     * @inheritdoc
     */
    public protected(set) array $parses = [
        Token\TList::class => Token\TListAsArray::class,
        Token\TArray::class => Token\TArrayAsShort::class,
        Token\TElse::class => Token\TElseAsElseIf::class,
        Token\TStringLiteral::class => Token\TStringLiteralAsSingleQuote::class,
    ];

    /**
     * @inheritdoc
     */
    public protected(set) array $rules = [
        Rule\RemoveBom::class => [],
        Rule\AddControlBraces::class => [],
        Rule\ExpandImports::class => [],
        Rule\RemoveUnusedImports::class => [],
        Rule\OrderImports::class => [],
        Rule\AddMissingVisibility::class => [],
        Rule\OrderModifiers::class => [],
        Rule\OrderTypes::class => [],
        Rule\MergeParenBracket::class => [],
        Rule\RejoinOrphans::class => [],
        Rule\NormalizeTrailingCommas::class => [],
        Rule\RemoveTrailingBlankLines::class => [],
        Rule\ConvertToYodaConditions::class => [],
        Rule\AddInstantiationParentheses::class => [],
        Rule\ConvertSwitchContinueToBreak::class => [],
        Rule\NormalizeMemberSpacing::class => [],
        Rule\RemoveParensFromLanguageConstructs::class => [],
        Rule\RemoveRepeatedSemicolons::class => [],
        Rule\ConvertImplicitInterpolation::class => [],
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
            $symfonyStyles[$class] = array_merge($symfonyStyles[$class] ?? [], $args);
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
