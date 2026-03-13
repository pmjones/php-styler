<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TStaticVarEndSemicolonTest extends TTestCase
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
                    static $bar = 1;
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
                    TStaticVarEndSemicolon::class,
                    TFunctionClosingBrace::class,
                ],
            ],
        ];
    }
}
