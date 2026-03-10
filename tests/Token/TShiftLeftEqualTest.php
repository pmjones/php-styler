<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TShiftLeftEqualTest extends TTestCase
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
                $foo <<= 2;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TShiftLeftEqual::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
