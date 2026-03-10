<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TBinaryPlusTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'binary-plus' => [
                '<?php $a + $b;',
                [
                    TPhpOpeningTagInline::class,
                    TVariable::class,
                    TBinaryPlus::class,
                    TVariable::class,
                    TSemicolon::class,
                ],
            ],
            'unary-plus' => [
                '<?php $a = +1;',
                [
                    TPhpOpeningTagInline::class,
                    TVariable::class,
                    TAssign::class,
                    TUnaryPlus::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                ],
            ],
            'binary-after-close-paren' => [
                '<?php ($a) + 1;',
                [
                    TPhpOpeningTagInline::class,
                    TExpressionOpeningParen::class,
                    TVariable::class,
                    TExpressionClosingParen::class,
                    TBinaryPlus::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
