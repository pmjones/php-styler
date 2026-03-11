<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TDivideEqualTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
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
