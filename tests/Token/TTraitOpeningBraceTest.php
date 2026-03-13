<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TTraitOpeningBraceTest extends TTestCase
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
                trait Foo {}
                CODE,
                [
                    TPhpOpeningTag::class,
                    TTrait::class,
                    TTraitName::class,
                    TTraitOpeningBrace::class,
                    TTraitClosingBrace::class,
                ],
            ],
        ];
    }
}
