<?php
declare(strict_types=1);

namespace Oxford\Token;

class TImplementsTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'class' => [
                <<<'CODE'
                <?php
                class Foo implements Bar, Baz\Dib, \Zim\Gir
                {
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TClass::class,
                    TClassName::class,
                    TImplements::class,
                    TUnqualifiedName::class,
                    TImplementsComma::class,
                    TQualifiedName::class,
                    TImplementsComma::class,
                    TFullyQualifiedName::class,
                    TClassOpeningBrace::class,
                    TClassClosingBrace::class,
                ],
            ],
            'enum' => [
                <<<'CODE'
                <?php
                enum Foo implements Bar, Baz\Dib, \Zim\Gir
                {
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TEnum::class,
                    TEnumName::class,
                    TImplements::class,
                    TUnqualifiedName::class,
                    TImplementsComma::class,
                    TQualifiedName::class,
                    TImplementsComma::class,
                    TFullyQualifiedName::class,
                    TEnumOpeningBrace::class,
                    TEnumClosingBrace::class,
                ],
            ],
        ];
    }
}
