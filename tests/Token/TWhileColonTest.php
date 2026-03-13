<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TWhileColonTest extends TTestCase
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
                while (true):
                    break;
                endwhile;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TWhile::class,
                    TWhileOpeningParen::class,
                    TTrue::class,
                    TWhileClosingParen::class,
                    TWhileColon::class,
                    TBreak::class,
                    TSemicolon::class,
                    TEndwhile::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
