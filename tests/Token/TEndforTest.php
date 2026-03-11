<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TEndforTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'endfor' => [
                <<<'CODE'
                <?php
                for ($i = 0; $i < 10; $i++):
                endfor;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TFor::class,
                    TForOpeningParen::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TForSemicolon::class,
                    TVariable::class,
                    TSmallerThan::class,
                    TIntegerLiteral::class,
                    TForSemicolon::class,
                    TVariable::class,
                    TPostIncrement::class,
                    TForClosingParen::class,
                    TForColon::class,
                    TEndfor::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
