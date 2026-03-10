<?php
declare(strict_types=1);

namespace Oxford\Token;

class TMagicLineConstantTest extends TTestCase
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
                $foo = __LINE__;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TMagicLineConstant::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
