<?php
declare(strict_types=1);

namespace Oxford\Token;

class TArrayElementClosingBracketTest extends TTestCase
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
