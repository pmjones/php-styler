<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TMatchReturnCommaTest extends TTestCase
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
                $foo = match(true) {
                    1 => 'a',
                    default => 'b',
                };
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TMatch::class,
                    TMatchOpeningParen::class,
                    TTrue::class,
                    TMatchClosingParen::class,
                    TMatchOpeningBrace::class,
                    TIntegerLiteral::class,
                    TMatchDoubleArrow::class,
                    TStringLiteral::class,
                    TMatchReturnComma::class,
                    TDefaultMatch::class,
                    TMatchDoubleArrow::class,
                    TStringLiteral::class,
                    TMatchReturnComma::class,
                    TMatchClosingBrace::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
