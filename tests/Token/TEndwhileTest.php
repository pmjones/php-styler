<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TEndwhileTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'endwhile' => [
                <<<'CODE'
                <?php
                while (true):
                endwhile;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TWhile::class,
                    TWhileOpeningParen::class,
                    TTrue::class,
                    TWhileClosingParen::class,
                    TWhileColon::class,
                    TEndwhile::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
