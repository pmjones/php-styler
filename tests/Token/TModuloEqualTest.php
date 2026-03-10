<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TModuloEqualTest extends TTestCase
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
                $foo %= 2;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TModuloEqual::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
