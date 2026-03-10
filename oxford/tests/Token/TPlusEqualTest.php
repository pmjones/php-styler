<?php
declare(strict_types=1);

namespace Oxford\Token;

class TPlusEqualTest extends TTestCase
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
                $foo += 1;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TPlusEqual::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
