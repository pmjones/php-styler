<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TBitwiseAndTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'bitwise-and' => [
                <<<'CODE'
                <?php
                $foo = 1 & 2;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TBitwiseAnd::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
