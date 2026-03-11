<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TShiftRightEqualTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
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
