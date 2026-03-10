<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TAttributeTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'string' => [
                <<<'CODE'
                <?php
                #[Foo]
                class Foo
                {
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TAttribute::class,
                    TUnqualifiedName::class,
                    TAttributeClosingBracket::class,
                    TClass::class,
                    TClassName::class,
                    TClassOpeningBrace::class,
                    TClassClosingBrace::class,
                ]
            ],
            'name-qualified' => [
                <<<'CODE'
                <?php
                #[Foo\Bar]
                class Foo
                {
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TAttribute::class,
                    TQualifiedName::class,
                    TAttributeClosingBracket::class,
                    TClass::class,
                    TClassName::class,
                    TClassOpeningBrace::class,
                    TClassClosingBrace::class,
                ]
            ],
            'name-fully-qualified' => [
                <<<'CODE'
                <?php
                #[\Foo\Bar]
                class Foo
                {
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TAttribute::class,
                    TFullyQualifiedName::class,
                    TAttributeClosingBracket::class,
                    TClass::class,
                    TClassName::class,
                    TClassOpeningBrace::class,
                    TClassClosingBrace::class,
                ]
            ],
            'params' => [
                <<<'CODE'
                <?php
                #[Foo()]
                class Foo
                {
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TAttribute::class,
                    TUnqualifiedName::class,
                    TArgsOpeningParen::class,
                    TArgsClosingParen::class,
                    TAttributeClosingBracket::class,
                    TClass::class,
                    TClassName::class,
                    TClassOpeningBrace::class,
                    TClassClosingBrace::class,
                ]
            ],
            'multiple' => [
                <<<'CODE'
                <?php
                #[Foo, Bar\Baz, \Dib\Zim,]
                class Foo
                {
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TAttribute::class,
                    TUnqualifiedName::class,
                    TAttributeComma::class,
                    TQualifiedName::class,
                    TAttributeComma::class,
                    TFullyQualifiedName::class,
                    TAttributeComma::class,
                    TAttributeClosingBracket::class,
                    TClass::class,
                    TClassName::class,
                    TClassOpeningBrace::class,
                    TClassClosingBrace::class,
                ]
            ],
            'multiple-params' => [
                <<<'CODE'
                <?php
                #[Foo(), Bar\Baz(), \Dib\Zim(),]
                class Foo
                {
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TAttribute::class,
                    TUnqualifiedName::class,
                    TArgsOpeningParen::class,
                    TArgsClosingParen::class,
                    TAttributeComma::class,
                    TQualifiedName::class,
                    TArgsOpeningParen::class,
                    TArgsClosingParen::class,
                    TAttributeComma::class,
                    TFullyQualifiedName::class,
                    TArgsOpeningParen::class,
                    TArgsClosingParen::class,
                    TAttributeComma::class,
                    TAttributeClosingBracket::class,
                    TClass::class,
                    TClassName::class,
                    TClassOpeningBrace::class,
                    TClassClosingBrace::class,
                ]
            ],
        ];
    }
}
