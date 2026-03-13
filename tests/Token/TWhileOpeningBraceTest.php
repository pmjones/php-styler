<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TWhileOpeningBraceTest extends TTestCase
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
                while (true) { break; }
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
