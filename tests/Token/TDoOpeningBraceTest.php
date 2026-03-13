<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TDoOpeningBraceTest extends TTestCase
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
                do {
                    $foo = 1;
                } while (true);
                CODE,
                [
                    TPhpOpeningTag::class,
                    TDo::class,
                    TDoOpeningBrace::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                    TDoContinuationBrace::class,
                    TWhile::class,
                    TWhileOpeningParen::class,
                    TTrue::class,
                    TWhileClosingParen::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
