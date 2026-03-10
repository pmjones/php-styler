<?php
declare(strict_types=1);

namespace Oxford\Token;

class TExtendsCommaTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'interface-extends-multiple' => [
                <<<'CODE'
                <?php
                interface Foo extends Bar, Baz {}
                CODE,
                [
                    TPhpOpeningTag::class,
                    TInterface::class,
                    TInterfaceName::class,
                    TExtends::class,
                    TUnqualifiedName::class,
                    TExtendsComma::class,
                    TUnqualifiedName::class,
                    TInterfaceOpeningBrace::class,
                    TInterfaceClosingBrace::class,
                ],
            ],
        ];
    }
}
