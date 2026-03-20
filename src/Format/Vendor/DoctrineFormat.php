<?php
declare(strict_types=1);

namespace PhpStyler\Format\Vendor;

use PhpStyler\Format\DeclarationFormat;
use PhpStyler\Rule;
use PhpStyler\Token;

class DoctrineFormat extends DeclarationFormat
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
        Rule\OrderTypes::class => ['order' => ['*', Token\TNull::class]],
        Rule\MergeParenBracket::class => [],
        Rule\NormalizeTrailingCommas::class => [],
        Rule\RemoveTrailingBlankLines::class => [],
        Rule\ConvertDoubleToSingleQuote::class => [],
        Rule\AddInstantiationParentheses::class => [],
        Rule\ConvertImplicitInterpolation::class => [],
        Rule\ConvertFromYodaConditions::class => [],
        Rule\ConvertLogicalOperators::class => [],
        Rule\RemoveParensFromLanguageConstructs::class => [],
        Rule\RemoveRepeatedSemicolons::class => [],
        Rule\ConvertHeredocToNowdoc::class => [],
        Rule\ExpandAttributes::class => [],
        Rule\NormalizeMemberSpacing::class => [],
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
        $doctrineStyles = [
            Token\TNot::class => ['spaceAfter' => true],
            Token\TIntCast::class => ['spaceAfter' => true],
            Token\TBoolCast::class => ['spaceAfter' => true],
            Token\TStringCast::class => ['spaceAfter' => true],
            Token\TArrayCast::class => ['spaceAfter' => true],
            Token\TObjectCast::class => ['spaceAfter' => true],
            Token\TDoubleCast::class => ['spaceAfter' => true],
            Token\TUnsetCast::class => ['spaceAfter' => true],
            Token\TPostIncrement::class => [
                'spaceBefore' => false,
                'spaceAfter' => false,
            ],
            Token\TPostDecrement::class => [
                'spaceBefore' => false,
                'spaceAfter' => false,
            ],
            Token\TPreIncrement::class => [
                'spaceBefore' => false,
                'spaceAfter' => false,
            ],
            Token\TPreDecrement::class => [
                'spaceBefore' => false,
                'spaceAfter' => false,
            ],
            Token\TFunctionCallName::class => ['case' => 'strtolower'],
            Token\TReturn::class => ['blankLineBefore' => true],
            Token\TThrow::class => ['blankLineBefore' => true],
            Token\TYield::class => ['blankLineBefore' => true],
            Token\TYieldFrom::class => ['blankLineBefore' => true],
        ];

        foreach ($styles as $class => $args) {
            $doctrineStyles[$class] = array_merge($doctrineStyles[$class] ?? [], $args);
        }

        parent::__construct(
            eol: $eol,
            lineLen: $lineLen,
            indentLen: $indentLen,
            indentTab: $indentTab,
            styles: $doctrineStyles,
            rules: $rules,
        );
        $this->setReturnTypeColonSpacing(false);
    }
}
