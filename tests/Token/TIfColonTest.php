<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TIfColonTest extends TTestCase
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
                endif;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TIf::class,
                    TIfOpeningParen::class,
                    TTrue::class,
                    TIfClosingParen::class,
                    TIfColon::class,
                    TEndif::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
