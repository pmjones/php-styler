<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TMultiplyTest extends TTestCase
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
                $foo = 2 * 3;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TMultiply::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
