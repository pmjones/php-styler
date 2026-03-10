<?php
declare(strict_types=1);

namespace Oxford\Token;

class TColonTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'case' => [
                <<<'CODE'
                <?php
                switch ($foo) {
                    case 1:
                        break;
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TSwitch::class,
                    TSwitchOpeningParen::class,
                    TVariable::class,
                    TSwitchClosingParen::class,
                    TSwitchOpeningBrace::class,
                    TCase::class,
                    TIntegerLiteral::class,
                    TCaseColon::class,
                    TBreak::class,
                    TSemicolon::class,
                    TSwitchAfterCaseClosingBrace::class,
                ],
            ],
            'alternative' => [
                <<<'CODE'
                <?php
                if ($foo):
                endif;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TIf::class,
                    TIfOpeningParen::class,
                    TVariable::class,
                    TIfClosingParen::class,
                    TIfColon::class,
                    TEndif::class,
                    TSemicolon::class,
                ],
            ],
            'return-type' => [
                <<<'CODE'
                <?php
                function foo() : int {
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TFunction::class,
                    TFunctionName::class,
                    TParamsOpeningParen::class,
                    TParamsClosingParen::class,
                    TReturnColon::class,
                    TInt::class,
                    TFunctionOpeningBrace::class,
                    TFunctionClosingBrace::class,
                ],
            ],
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
        ];
    }
}
