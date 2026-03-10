<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TGlobalTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'global-one' => [
                <<<'CODE'
                <?php
                function foo() {
                    global $bar;
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TFunction::class,
                    TFunctionName::class,
                    TParamsOpeningParen::class,
                    TParamsClosingParen::class,
                    TFunctionOpeningBrace::class,
                    TGlobal::class,
                    TVariable::class,
                    TGlobalEndSemicolon::class,
                    TFunctionClosingBrace::class,
                ],
            ],
            'global-many' => [
                <<<'CODE'
                <?php
                function foo() {
                    global $bar, $baz, $dib;
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TFunction::class,
                    TFunctionName::class,
                    TParamsOpeningParen::class,
                    TParamsClosingParen::class,
                    TFunctionOpeningBrace::class,
                    TGlobal::class,
                    TVariable::class,
                    TGlobalComma::class,
                    TVariable::class,
                    TGlobalComma::class,
                    TVariable::class,
                    TGlobalEndSemicolon::class,
                    TFunctionClosingBrace::class,
                ],
            ],
        ];
    }
}
