<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TElseClosingBraceTest extends TTestCase
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
                } else {
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
                    TElse::class,
                    TElseOpeningBrace::class,
                    TElseClosingBrace::class,
                ],
            ],
        ];
    }
}
