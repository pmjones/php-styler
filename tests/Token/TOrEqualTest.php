<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TOrEqualTest extends TTestCase
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
                $foo |= 1;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TOrEqual::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
