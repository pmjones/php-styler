<?php
declare(strict_types=1);

namespace Oxford\Token;

class TClosingBraceTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'basic' => [
                <<<'CODE'
                <?php
                if (true) {
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TIf::class,
                    TIfOpeningParen::class,
                    TTrue::class,
                    TIfClosingParen::class,
                    TIfOpeningBrace::class,
                    TIfClosingBrace::class,
                ],
            ],
        ];
    }
}
