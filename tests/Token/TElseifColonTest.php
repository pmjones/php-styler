<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TElseifColonTest extends TTestCase
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
                elseif (false):
                endif;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TIf::class,
                    TIfOpeningParen::class,
                    TTrue::class,
                    TIfClosingParen::class,
                    TIfColon::class,
                    TElseif::class,
                    TElseifOpeningParen::class,
                    TFalse::class,
                    TElseifClosingParen::class,
                    TElseifColon::class,
                    TEndif::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
