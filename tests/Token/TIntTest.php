<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TIntTest extends TTestCase
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
                function foo(int $bar) {}
                CODE,
                [
                    TPhpOpeningTag::class,
                    TFunction::class,
                    TFunctionName::class,
                    TParamsOpeningParen::class,
                    TInt::class,
                    TVariable::class,
                    TParamsClosingParen::class,
                    TFunctionOpeningBrace::class,
                    TFunctionClosingBrace::class,
                ],
            ],
        ];
    }
}
