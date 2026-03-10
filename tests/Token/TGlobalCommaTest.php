<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TGlobalCommaTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'global-multiple' => [
                <<<'CODE'
                <?php
                function foo() {
                    global $a, $b;
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
                    TGlobalEndSemicolon::class,
                    TFunctionClosingBrace::class,
                ],
            ],
        ];
    }
}
