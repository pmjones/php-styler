<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TElseColonTest extends TTestCase
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
                if (true):
                else:
                endif;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TIf::class,
                    TIfOpeningParen::class,
                    TTrue::class,
                    TIfClosingParen::class,
                    TIfColon::class,
                    TElse::class,
                    TElseColon::class,
                    TEndif::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
