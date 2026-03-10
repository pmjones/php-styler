<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TXorEqualTest extends TTestCase
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
                $foo ^= 1;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TXorEqual::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
