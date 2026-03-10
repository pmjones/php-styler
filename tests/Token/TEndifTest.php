<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TEndifTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'endif' => [
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
