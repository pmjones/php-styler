<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TYieldDoubleArrowTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'basic' => [
                <<<'CODE'
                <?php
                function foo() {
                    yield 'a' => 1;
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
