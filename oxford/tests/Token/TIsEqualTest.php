<?php
declare(strict_types=1);

namespace Oxford\Token;

class TIsEqualTest extends TTestCase
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
                $foo == $bar;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TIsEqual::class,
                    TVariable::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
