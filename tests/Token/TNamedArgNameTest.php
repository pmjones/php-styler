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
