<?php
declare(strict_types=1);

namespace Oxford\Token;

class TMagicDirConstantTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'magic-constant' => [
                <<<'CODE'
                <?php
                $foo = __DIR__;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TMagicDirConstant::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
