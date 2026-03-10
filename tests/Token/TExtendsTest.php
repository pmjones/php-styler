<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TExtendsTest extends TTestCase
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
                class Foo extends Bar
                {
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TClass::class,
                    TClassName::class,
                    TExtends::class,
                    TUnqualifiedName::class,
                    TClassOpeningBrace::class,
                    TClassClosingBrace::class,
                ],
            ],
            'interface' => [
                <<<'CODE'
                <?php
                interface Foo extends Bar, Baz\Dib, \Zim\Gir
                {
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TInterface::class,
                    TInterfaceName::class,
                    TExtends::class,
                    TUnqualifiedName::class,
                    TExtendsComma::class,
                    TQualifiedName::class,
                    TExtendsComma::class,
                    TFullyQualifiedName::class,
                    TInterfaceOpeningBrace::class,
                    TInterfaceClosingBrace::class,
                ],
            ],
        ];
    }
}
