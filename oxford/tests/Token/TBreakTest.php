<?php
declare(strict_types=1);

namespace Oxford\Token;

class TBreakTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
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
