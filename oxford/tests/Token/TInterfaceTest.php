<?php
declare(strict_types=1);

namespace Oxford\Token;

class TInterfaceTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'basic' => [
                <<<'CODE'
                <?php
                interface Foo
                {
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TInterface::class,
                    TInterfaceName::class,
                    TInterfaceOpeningBrace::class,
                    TInterfaceClosingBrace::class,
                ],
            ],
        ];
    }
}
