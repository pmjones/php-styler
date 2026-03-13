<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TGlobalEndSemicolonTest extends TTestCase
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
        ];
    }
}
