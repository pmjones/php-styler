<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TFinalTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'class' => [
                <<<'CODE'
                <?php
                final class Foo
                {
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TFinal::class,
                    TClass::class,
                    TClassName::class,
                    TClassOpeningBrace::class,
                    TClassClosingBrace::class,
                ],
            ],
            'method' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    final function bar()
                    {
                    }
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TClass::class,
                    TClassName::class,
                    TClassOpeningBrace::class,
                    TFinal::class,
                    TFunction::class,
                    TFunctionName::class,
                    TParamsOpeningParen::class,
                    TParamsClosingParen::class,
                    TFunctionOpeningBrace::class,
                    TFunctionClosingBrace::class,
                    TClassClosingBrace::class,
                ],
            ],
        ];
    }
}
