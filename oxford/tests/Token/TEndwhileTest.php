<?php
declare(strict_types=1);

namespace Oxford\Token;

class TEndwhileTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
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
