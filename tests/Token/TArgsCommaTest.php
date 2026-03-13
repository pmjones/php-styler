<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TArgsCommaTest extends TTestCase
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
                foo(1, 2, 3);
                CODE,
                [
                    TPhpOpeningTag::class,
                    TFunctionCallName::class,
                    TArgsOpeningParen::class,
                    TIntegerLiteral::class,
                    TArgsComma::class,
                    TIntegerLiteral::class,
                    TArgsComma::class,
                    TIntegerLiteral::class,
                    TArgsClosingParen::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
