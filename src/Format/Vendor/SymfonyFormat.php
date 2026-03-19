<?php
declare(strict_types=1);

namespace PhpStyler\Format\Vendor;

use PhpStyler\Format\DeclarationFormat;
use PhpStyler\Rule;

class SymfonyFormat extends DeclarationFormat
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
        Rule\ConvertDoubleToSingleQuote::class => [],
        Rule\ExpandImports::class => [],
        Rule\RemoveUnusedImports::class => [],
        Rule\OrderImports::class => [],
        Rule\AddMissingVisibility::class => [],
        Rule\OrderModifiers::class => [],
        Rule\OrderTypes::class => [],
        Rule\MergeParenBracket::class => [],
        Rule\NormalizeTrailingCommas::class => [],
        Rule\RemoveTrailingBlankLines::class => [],
        Rule\ConvertToYodaConditions::class => [],
        Rule\AddBlankLineBeforeReturn::class => [],
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
        parent::__construct(
            eol: $eol,
            lineLen: $lineLen,
            indentLen: $indentLen,
            indentTab: $indentTab,
            styles: $styles,
            rules: $rules,
        );
        $this->setConcatenationSpacing(false);
        $this->setReturnTypeColonSpacing(false);
    }
}
