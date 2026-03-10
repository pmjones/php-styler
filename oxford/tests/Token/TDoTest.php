<?php
declare(strict_types=1);

namespace Oxford\Token;

class TDoTest extends TTestCase
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
                do {
                } while ($foo);
                CODE,
                [
                    TPhpOpeningTag::class,
                    TDo::class,
                    TDoOpeningBrace::class,
                    TDoContinuationBrace::class,
                    TWhile::class,
                    TWhileOpeningParen::class,
                    TVariable::class,
                    TWhileClosingParen::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
