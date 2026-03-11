<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TNewTest extends TTestCase
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
                $foo = new Foo();
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TNew::class,
                    TUnqualifiedName::class,
                    TArgsOpeningParen::class,
                    TArgsClosingParen::class,
                    TSemicolon::class,
                ],
            ],
            'anonymous-class' => [
                <<<'CODE'
                <?php
                $foo = new class () {
                };
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TNew::class,
                    TAnonymousClass::class,
                    TAnonymousClassArgsOpeningParen::class,
                    TAnonymousClassArgsClosingParen::class,
                    TAnonymousOpeningBrace::class,
                    TAnonymousClosingBrace::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
