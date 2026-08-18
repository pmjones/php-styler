<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TNamedArgNameTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'named-arg' => [
                <<<'CODE'
                <?php
                foo(name: 'bar');
                CODE,
                [
                    TPhpOpeningTag::class,
                    TFunctionCallName::class,
                    TArgsOpeningParen::class,
                    TNamedArgName::class,
                    TNamedArgColon::class,
                    TStringLiteral::class,
                    TArgsClosingParen::class,
                    TSemicolon::class,
                ],
            ],
            'named-arg-in-anonymous-class' => [
                <<<'CODE'
                <?php
                new class (name: 'bar') {};
                CODE,
                [
                    TPhpOpeningTag::class,
                    TNew::class,
                    TAnonymousClass::class,
                    TAnonymousClassArgsOpeningParen::class,
                    TNamedArgName::class,
                    TNamedArgColon::class,
                    TStringLiteral::class,
                    TAnonymousClassArgsClosingParen::class,
                    TAnonymousOpeningBrace::class,
                    TAnonymousClosingBrace::class,
                    TSemicolon::class,
                ],
            ],
            'keyword-named-arg-in-anonymous-class' => [
                <<<'CODE'
                <?php
                new class (array: 'bar') {};
                CODE,
                [
                    TPhpOpeningTag::class,
                    TNew::class,
                    TAnonymousClass::class,
                    TAnonymousClassArgsOpeningParen::class,
                    TNamedArgName::class,
                    TNamedArgColon::class,
                    TStringLiteral::class,
                    TAnonymousClassArgsClosingParen::class,
                    TAnonymousOpeningBrace::class,
                    TAnonymousClosingBrace::class,
                    TSemicolon::class,
                ],
            ],
            'named-arg-in-attribute' => [
                <<<'CODE'
                <?php
                #[Foo(name: 'bar')]
                class Baz {}
                CODE,
                [
                    TPhpOpeningTag::class,
                    TAttribute::class,
                    TUnqualifiedName::class,
                    TArgsOpeningParen::class,
                    TNamedArgName::class,
                    TNamedArgColon::class,
                    TStringLiteral::class,
                    TArgsClosingParen::class,
                    TAttributeClosingBracket::class,
                    TClass::class,
                    TClassName::class,
                    TClassOpeningBrace::class,
                    TClassClosingBrace::class,
                ],
            ],
        ];
    }
}
