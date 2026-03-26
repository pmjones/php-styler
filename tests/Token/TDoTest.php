<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TDoTest extends TTestCase
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
                    TDoWhileEndSemicolon::class,
                ],
            ],
        ];
    }
}
