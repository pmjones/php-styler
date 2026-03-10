<?php
declare(strict_types=1);

namespace Oxford\Token;

class TEnumTest extends TTestCase
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
                enum Foo
                {
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TEnum::class,
                    TEnumName::class,
                    TEnumOpeningBrace::class,
                    TEnumClosingBrace::class,
                ],
            ],
        ];
    }
}
