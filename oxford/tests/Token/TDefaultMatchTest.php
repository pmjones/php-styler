<?php
declare(strict_types=1);

namespace Oxford\Token;

class TDefaultMatchTest extends TTestCase
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
                $foo = match ($bar) {
                    default => 'baz',
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
