<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TForCommaTest extends TTestCase
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
                for ($i = 0, $j = 1; $i < 10; $i++, $j++) {
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TFor::class,
                    TForOpeningParen::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TForComma::class,
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
                    TForComma::class,
                    TVariable::class,
                    TPostIncrement::class,
                    TForClosingParen::class,
                    TForOpeningBrace::class,
                    TForClosingBrace::class,
                ],
            ],
        ];
    }
}
