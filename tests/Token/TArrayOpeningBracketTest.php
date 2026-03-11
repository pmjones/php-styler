<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TArrayOpeningBracketTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'empty' => [
                <<<'CODE'
                <?php
                [];
                CODE,
                [
                    TPhpOpeningTag::class,
                    TArrayOpeningBracket::class,
                    TArrayClosingBracket::class,
                    TSemicolon::class,
                ],
            ],
            'list' => [
                <<<'CODE'
                <?php
                ['foo', 'bar', 'baz'];
                CODE,
                [
                    TPhpOpeningTag::class,
                    TArrayOpeningBracket::class,
                    TStringLiteral::class,
                    TArrayComma::class,
                    TStringLiteral::class,
                    TArrayComma::class,
                    TStringLiteral::class,
                    TArrayClosingBracket::class,
                    TSemicolon::class,
                ],
            ],
            'numeric-key' => [
                <<<'CODE'
                <?php
                [1 => 'foo', 2 => 'bar', 3 => 'baz'];
                CODE,
                [
                    TPhpOpeningTag::class,
                    TArrayOpeningBracket::class,
                    TIntegerLiteral::class,
                    TArrayDoubleArrow::class,
                    TStringLiteral::class,
                    TArrayComma::class,
                    TIntegerLiteral::class,
                    TArrayDoubleArrow::class,
                    TStringLiteral::class,
                    TArrayComma::class,
                    TIntegerLiteral::class,
                    TArrayDoubleArrow::class,
                    TStringLiteral::class,
                    TArrayClosingBracket::class,
                    TSemicolon::class,
                ],
            ],
            'string-key' => [
                <<<'CODE'
                <?php
                ['foo' => 1, 'bar' => 2, 'baz' => 3];
                CODE,
                [
                    TPhpOpeningTag::class,
                    TArrayOpeningBracket::class,
                    TStringLiteral::class,
                    TArrayDoubleArrow::class,
                    TIntegerLiteral::class,
                    TArrayComma::class,
                    TStringLiteral::class,
                    TArrayDoubleArrow::class,
                    TIntegerLiteral::class,
                    TArrayComma::class,
                    TStringLiteral::class,
                    TArrayDoubleArrow::class,
                    TIntegerLiteral::class,
                    TArrayClosingBracket::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
