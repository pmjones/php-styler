<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TForTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'braced' => [
                <<<'CODE'
                <?php
                for ($i = 0; $i < 88; $i ++) {
                    $k += $i;
                }
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
                    TForOpeningBrace::class,
                    TVariable::class,
                    TPlusEqual::class,
                    TVariable::class,
                    TSemicolon::class,
                    TForClosingBrace::class,
                ],
            ],
            'braced-crazy' => [
                <<<'CODE'
                <?php
                for (
                    $a = 10, $b = 20, $c = 30;
                    $a < 100, $b < 200, $c < 300;
                    $a += 10, $b += 10, $c += 10
                ) {
                    $k += 1;
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
                    TForComma::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TForSemicolon::class,
                    TVariable::class,
                    TSmallerThan::class,
                    TIntegerLiteral::class,
                    TForComma::class,
                    TVariable::class,
                    TSmallerThan::class,
                    TIntegerLiteral::class,
                    TForComma::class,
                    TVariable::class,
                    TSmallerThan::class,
                    TIntegerLiteral::class,
                    TForSemicolon::class,
                    TVariable::class,
                    TPlusEqual::class,
                    TIntegerLiteral::class,
                    TForComma::class,
                    TVariable::class,
                    TPlusEqual::class,
                    TIntegerLiteral::class,
                    TForComma::class,
                    TVariable::class,
                    TPlusEqual::class,
                    TIntegerLiteral::class,
                    TForClosingParen::class,
                    TForOpeningBrace::class,
                    TVariable::class,
                    TPlusEqual::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                    TForClosingBrace::class,
                ],
            ],
            'alternative' => [
                <<<'CODE'
                <?php
                for ($i = 0; $i < 88; $i ++):
                    $k += $i;
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
                    TVariable::class,
                    TPlusEqual::class,
                    TVariable::class,
                    TSemicolon::class,
                    TEndfor::class,
                    TSemicolon::class,
                ],
            ],
            'alternative-crazy' => [
                <<<'CODE'
                <?php
                for (
                    $a = 10, $b = 20, $c = 30;
                    $a < 100, $b < 200, $c < 300;
                    $a += 10, $b += 10, $c += 10
                ):
                    $k += $i;
                endfor;
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
                    TForComma::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TForSemicolon::class,
                    TVariable::class,
                    TSmallerThan::class,
                    TIntegerLiteral::class,
                    TForComma::class,
                    TVariable::class,
                    TSmallerThan::class,
                    TIntegerLiteral::class,
                    TForComma::class,
                    TVariable::class,
                    TSmallerThan::class,
                    TIntegerLiteral::class,
                    TForSemicolon::class,
                    TVariable::class,
                    TPlusEqual::class,
                    TIntegerLiteral::class,
                    TForComma::class,
                    TVariable::class,
                    TPlusEqual::class,
                    TIntegerLiteral::class,
                    TForComma::class,
                    TVariable::class,
                    TPlusEqual::class,
                    TIntegerLiteral::class,
                    TForClosingParen::class,
                    TForColon::class,
                    TVariable::class,
                    TPlusEqual::class,
                    TVariable::class,
                    TSemicolon::class,
                    TEndfor::class,
                    TSemicolon::class,
                ],
            ],
            'unbraced' => [
                <<<'CODE'
                <?php
                for ($i = 0; $i < 88; $i ++) $k += $i;
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
                    TForOpeningBrace::class,
                    TVariable::class,
                    TPlusEqual::class,
                    TVariable::class,
                    TSemicolon::class,
                    TForClosingBrace::class,
                ],
            ],
            'unbraced-crazy' => [
                <<<'CODE'
                <?php
                for (
                    $a = 10, $b = 20, $c = 30;
                    $a < 100, $b < 200, $c < 300;
                    $a += 10, $b += 10, $c += 10
                ) $k += 1;
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
                    TForComma::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TForSemicolon::class,
                    TVariable::class,
                    TSmallerThan::class,
                    TIntegerLiteral::class,
                    TForComma::class,
                    TVariable::class,
                    TSmallerThan::class,
                    TIntegerLiteral::class,
                    TForComma::class,
                    TVariable::class,
                    TSmallerThan::class,
                    TIntegerLiteral::class,
                    TForSemicolon::class,
                    TVariable::class,
                    TPlusEqual::class,
                    TIntegerLiteral::class,
                    TForComma::class,
                    TVariable::class,
                    TPlusEqual::class,
                    TIntegerLiteral::class,
                    TForComma::class,
                    TVariable::class,
                    TPlusEqual::class,
                    TIntegerLiteral::class,
                    TForClosingParen::class,
                    TForOpeningBrace::class,
                    TVariable::class,
                    TPlusEqual::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                    TForClosingBrace::class,
                ],
            ],
        ];
    }
}
