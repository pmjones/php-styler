<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TElseifContinuationBraceTest extends TTestCase
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
                if (true) {
                } elseif (false) {
                } elseif (true) {
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TIf::class,
                    TIfOpeningParen::class,
                    TTrue::class,
                    TIfClosingParen::class,
                    TIfOpeningBrace::class,
                    TIfContinuationBrace::class,
                    TElseif::class,
                    TElseifOpeningParen::class,
                    TFalse::class,
                    TElseifClosingParen::class,
                    TElseifOpeningBrace::class,
                    TElseifContinuationBrace::class,
                    TElseif::class,
                    TElseifOpeningParen::class,
                    TTrue::class,
                    TElseifClosingParen::class,
                    TElseifOpeningBrace::class,
                    TElseifClosingBrace::class,
                ],
            ],
        ];
    }
}
