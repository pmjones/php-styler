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
        /** @php-styler-expansive */
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
                ],
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
                ],
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
                ],
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
                    TAttributeClosingBracket::class,
                    TClass::class,
                    TClassName::class,
                    TClassOpeningBrace::class,
                    TClassClosingBrace::class,
                ],
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
                    TAttributeClosingBracket::class,
                    TAttribute::class,
                    TQualifiedName::class,
                    TAttributeClosingBracket::class,
                    TAttribute::class,
                    TFullyQualifiedName::class,
                    TAttributeClosingBracket::class,
                    TClass::class,
                    TClassName::class,
                    TClassOpeningBrace::class,
                    TClassClosingBrace::class,
                ],
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
                    TAttributeClosingBracket::class,
                    TAttribute::class,
                    TQualifiedName::class,
                    TAttributeClosingBracket::class,
                    TAttribute::class,
                    TFullyQualifiedName::class,
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
