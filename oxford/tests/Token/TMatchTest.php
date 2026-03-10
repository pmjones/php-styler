<?php
declare(strict_types=1);

namespace Oxford\Token;

class TMatchTest extends TTestCase
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
                $foo = match ($foo) {
                    'foo' => 'FOO',
                    'bar',
                    'baz' => 'DIB',
                    default => 'ZIM',
                };
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TMatch::class,
                    TMatchOpeningParen::class,
                    TVariable::class,
                    TMatchClosingParen::class,
                    TMatchOpeningBrace::class,
                    TStringLiteral::class,
                    TMatchDoubleArrow::class,
                    TStringLiteral::class,
                    TMatchReturnComma::class,
                    TStringLiteral::class,
                    TMatchArmComma::class,
                    TStringLiteral::class,
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
