<?php
declare(strict_types=1);

namespace Oxford\Token;

class TTraitTest extends TTestCase
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
                trait Foo
                {
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TTrait::class,
                    TTraitName::class,
                    TTraitOpeningBrace::class,
                    TTraitClosingBrace::class,
                ]
            ],
        ];
    }
}
