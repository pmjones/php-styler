<?php
declare(strict_types=1);

namespace Oxford\Token;

class TOpeningBracketTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'array' => [
                <<<'CODE'
                <?php
                $foo = [1, 2];
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TArrayOpeningBracket::class,
                    TIntegerLiteral::class,
                    TArrayComma::class,
                    TIntegerLiteral::class,
                    TArrayClosingBracket::class,
                    TSemicolon::class,
                ],
            ],
            'element' => [
                <<<'CODE'
                <?php
                $foo = $bar[0];
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TVariable::class,
                    TArrayElementOpeningBracket::class,
                    TIntegerLiteral::class,
                    TArrayElementClosingBracket::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
