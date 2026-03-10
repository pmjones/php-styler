<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TLogicalXorTest extends TTestCase
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
                $foo xor $bar;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TLogicalXor::class,
                    TVariable::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
