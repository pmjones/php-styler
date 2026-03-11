<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TInterfaceTest extends TTestCase
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
