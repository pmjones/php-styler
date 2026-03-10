<?php
declare(strict_types=1);

namespace Oxford\Token;

class TQuestionTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'ternary' => [
                <<<'CODE'
                <?php
                $foo = $bar ? 1 : 2;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TVariable::class,
                    TTernaryQuestion::class,
                    TIntegerLiteral::class,
                    TTernaryColon::class,
                    TIntegerLiteral::class,
                    TTernaryEndSemicolon::class,
                ],
            ],
            'nullable' => [
                <<<'CODE'
                <?php
                function foo() : ?int {
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TFunction::class,
                    TFunctionName::class,
                    TParamsOpeningParen::class,
                    TParamsClosingParen::class,
                    TReturnColon::class,
                    TNullable::class,
                    TInt::class,
                    TFunctionOpeningBrace::class,
                    TFunctionClosingBrace::class,
                ],
            ],
        ];
    }
}
