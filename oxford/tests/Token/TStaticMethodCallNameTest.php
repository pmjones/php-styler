<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TStaticMethodCallNameTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'self' => [
                <<<'CODE'
                <?php
                self::foo();
                CODE,
                [
                    TPhpOpeningTag::class,
                    TSelf::class,
                    TMemberDoubleColon::class,
                    TStaticMethodCallName::class,
                    TArgsOpeningParen::class,
                    TArgsClosingParen::class,
                    TSemicolon::class,
                ],
            ],
            'class-name' => [
                <<<'CODE'
                <?php
                Foo::bar();
                CODE,
                [
                    TPhpOpeningTag::class,
                    TUnqualifiedName::class,
                    TMemberDoubleColon::class,
                    TStaticMethodCallName::class,
                    TArgsOpeningParen::class,
                    TArgsClosingParen::class,
                    TSemicolon::class,
                ],
            ],
            'static-binding' => [
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
        ];
    }
}
