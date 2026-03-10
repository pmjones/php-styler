<?php
declare(strict_types=1);

namespace Oxford\Token;

class TImplementsCommaTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'implements-multiple' => [
                <<<'CODE'
                <?php
                class Foo implements Bar, Baz {}
                CODE,
                [
                    TPhpOpeningTag::class,
                    TClass::class,
                    TClassName::class,
                    TImplements::class,
                    TUnqualifiedName::class,
                    TImplementsComma::class,
                    TUnqualifiedName::class,
                    TClassOpeningBrace::class,
                    TClassClosingBrace::class,
                ],
            ],
            'enum-implements-multiple' => [
                <<<'CODE'
                <?php
                enum Foo implements Bar, Baz {}
                CODE,
                [
                    TPhpOpeningTag::class,
                    TEnum::class,
                    TEnumName::class,
                    TImplements::class,
                    TUnqualifiedName::class,
                    TImplementsComma::class,
                    TUnqualifiedName::class,
                    TEnumOpeningBrace::class,
                    TEnumClosingBrace::class,
                ],
            ],
        ];
    }
}
