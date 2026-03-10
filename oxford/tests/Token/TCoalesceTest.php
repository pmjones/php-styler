<?php
declare(strict_types=1);

namespace Oxford\Token;

class TCoalesceTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'null-coalesce' => [
                <<<'CODE'
                <?php
                $foo = $bar ?? $baz;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TVariable::class,
                    TCoalesce::class,
                    TVariable::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
