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
    public protected(set) array $parseAs = [
        Token\TList::class => Token\TListAsArray::class,
        Token\TArray::class => Token\TArrayAsShort::class,
        Token\TLogicalAnd::class => Token\TLogicalAndAsBooleanAnd::class,
        Token\TLogicalOr::class => Token\TLogicalOrAsBooleanOr::class,
        Token\TElse::class => Token\TElseAsElseIf::class,
        Token\TStringLiteral::class => Token\TStringLiteralAsSingleQuote::class,
        Token\THeredocStart::class => Token\THeredocStartAsNowdoc::class,
        Token\TSemicolon::class => Token\TSemicolonSkipRepeats::class,
        Token\TVariable::class => Token\TVariableWithExplicitInterpolation::class,
    ];

    /**
     * @inheritdoc
     */
    public protected(set) array $rules = [
        Rule\RemoveBom::class => [],
        // import cleanup
        Rule\NormalizeImports::class => [],
        Rule\OrderTypes::class => ['order' => ['*', Token\TNull::class]],
        Rule\MergeParenBracket::class => [],
        Rule\RejoinOrphans::class => [],
        Rule\NormalizeTrailingCommas::class => [],
        Rule\RemoveTrailingBlankLines::class => [],
        Rule\ConvertFromYodaConditions::class => [],
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
            $doctrineStyles[$class] = array_merge(
                $doctrineStyles[$class] ?? [],
                $args,
            );
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
