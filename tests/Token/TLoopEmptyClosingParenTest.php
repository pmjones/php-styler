<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TLoopEmptyClosingParenTest extends TTestCase
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
                for (;;) {
                    break;
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TFor::class,
                    TForOpeningParen::class,
                    TLoopEmptySemicolon::class,
                    TLoopEmptySemicolon::class,
                    TLoopEmptyClosingParen::class,
                    TForOpeningBrace::class,
                    TBreak::class,
                    TSemicolon::class,
                    TForClosingBrace::class,
                ],
            ],
        ];
    }
}
