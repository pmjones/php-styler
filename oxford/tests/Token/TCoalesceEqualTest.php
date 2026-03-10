<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TCoalesceEqualTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'null-coalesce-equal' => [
                <<<'CODE'
                <?php
                $foo ??= $bar;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TCoalesceEqual::class,
                    TVariable::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
