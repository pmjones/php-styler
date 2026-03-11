<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TStaticBindingTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'method-call' => [
                <<<'CODE'
                <?php
                static::foo();
                CODE,
                [
                    TPhpOpeningTag::class,
                    TStaticBinding::class,
                    TMemberDoubleColon::class,
                    TStaticMethodCallName::class,
                    TArgsOpeningParen::class,
                    TArgsClosingParen::class,
                    TSemicolon::class,
                ],
            ],
            'property-access' => [
                <<<'CODE'
                <?php
                static::$bar;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TStaticBinding::class,
                    TMemberDoubleColon::class,
                    TVariable::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
