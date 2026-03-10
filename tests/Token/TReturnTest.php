<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TReturnTest extends TTestCase
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
                    return 1;
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TFunction::class,
                    TFunctionName::class,
                    TParamsOpeningParen::class,
                    TParamsClosingParen::class,
                    TFunctionOpeningBrace::class,
                    TReturn::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                    TFunctionClosingBrace::class,
                ],
            ],
        ];
    }
}
