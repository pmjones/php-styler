<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TBreakTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'break' => [
                <<<'CODE'
                <?php
                while (true) {
                    break;
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TWhile::class,
                    TWhileOpeningParen::class,
                    TTrue::class,
                    TWhileClosingParen::class,
                    TWhileOpeningBrace::class,
                    TBreak::class,
                    TSemicolon::class,
                    TWhileClosingBrace::class,
                ],
            ],
        ];
    }
}
