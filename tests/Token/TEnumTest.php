<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TEnumTest extends TTestCase
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
