<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TStaticCommaTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'static-var-multiple' => [
                <<<'CODE'
                <?php
                function foo() {
                    static $a = 1, $b = 2;
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TFunction::class,
                    TFunctionName::class,
                    TParamsOpeningParen::class,
                    TParamsClosingParen::class,
                    TFunctionOpeningBrace::class,
                    TStaticVar::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TStaticComma::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TStaticVarEndSemicolon::class,
                    TFunctionClosingBrace::class,
                ],
            ],
        ];
    }
}
