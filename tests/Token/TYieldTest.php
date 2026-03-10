<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TYieldTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'basic' => [
                <<<'CODE'
                <?php
                function foo() {
                    yield 1;
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TFunction::class,
                    TFunctionName::class,
                    TParamsOpeningParen::class,
                    TParamsClosingParen::class,
                    TFunctionOpeningBrace::class,
                    TYield::class,
                    TIntegerLiteral::class,
                    TYieldEndSemicolon::class,
                    TFunctionClosingBrace::class,
                ],
            ],
            'key-value' => [
                <<<'CODE'
                <?php
                function foo() {
                    yield 'bar' => 1;
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TFunction::class,
                    TFunctionName::class,
                    TParamsOpeningParen::class,
                    TParamsClosingParen::class,
                    TFunctionOpeningBrace::class,
                    TYield::class,
                    TStringLiteral::class,
                    TYieldDoubleArrow::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                    TFunctionClosingBrace::class,
                ],
            ],
        ];
    }
}
