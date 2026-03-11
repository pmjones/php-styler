<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TFnTest extends TTestCase
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
                $foo = 1;
                $bar = fn () => $foo;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                    TVariable::class,
                    TAssign::class,
                    TFn::class,
                    TParamsOpeningParen::class,
                    TParamsClosingParen::class,
                    TFnDoubleArrow::class,
                    TVariable::class,
                    TFnEndSemicolon::class,
                ],
            ],
            'return' => [
                <<<'CODE'
                <?php
                $foo = 1;
                $bar = fn () : int => $foo;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                    TVariable::class,
                    TAssign::class,
                    TFn::class,
                    TParamsOpeningParen::class,
                    TParamsClosingParen::class,
                    TReturnColon::class,
                    TInt::class,
                    TFnDoubleArrow::class,
                    TVariable::class,
                    TFnEndSemicolon::class,
                ],
            ],
        ];
    }
}
