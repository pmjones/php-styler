<?php
declare(strict_types=1);

namespace Oxford\Token;

class TMagicClassConstantTest extends TTestCase
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
                $foo = __CLASS__;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TMagicClassConstant::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
