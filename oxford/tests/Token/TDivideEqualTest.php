<?php
declare(strict_types=1);

namespace Oxford\Token;

class TDivideEqualTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'div-equal' => [
                <<<'CODE'
                <?php
                $foo /= 2;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TDivideEqual::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
