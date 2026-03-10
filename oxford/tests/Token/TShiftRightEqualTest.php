<?php
declare(strict_types=1);

namespace Oxford\Token;

class TShiftRightEqualTest extends TTestCase
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
                $foo >>= 2;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TShiftRightEqual::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
