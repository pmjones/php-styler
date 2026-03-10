<?php
declare(strict_types=1);

namespace Oxford\Token;

class TUnaryMinusTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'binary-minus' => [
                '<?php $a - $b;',
                [
                    TPhpOpeningTagInline::class,
                    TVariable::class,
                    TBinaryMinus::class,
                    TVariable::class,
                    TSemicolon::class,
                ],
            ],
            'unary-minus' => [
                '<?php $a = -1;',
                [
                    TPhpOpeningTagInline::class,
                    TVariable::class,
                    TAssign::class,
                    TUnaryMinus::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                ],
            ],
            'unary-after-open-paren' => [
                '<?php foo(-1);',
                [
                    TPhpOpeningTagInline::class,
                    TFunctionCallName::class,
                    TArgsOpeningParen::class,
                    TUnaryMinus::class,
                    TIntegerLiteral::class,
                    TArgsClosingParen::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
