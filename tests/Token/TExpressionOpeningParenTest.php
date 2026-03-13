<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TExpressionOpeningParenTest extends TTestCase
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
                $foo = (1 + 2);
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TExpressionOpeningParen::class,
                    TIntegerLiteral::class,
                    TBinaryPlus::class,
                    TIntegerLiteral::class,
                    TExpressionClosingParen::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
