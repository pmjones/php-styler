<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TYieldEndSemicolonTest extends TTestCase
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
        ];
    }
}
