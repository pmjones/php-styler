<?php
declare(strict_types=1);

namespace Oxford\Token;

class TEndswitchTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'endswitch' => [
                <<<'CODE'
                <?php
                switch ($foo):
                endswitch;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TSwitch::class,
                    TSwitchOpeningParen::class,
                    TVariable::class,
                    TSwitchClosingParen::class,
                    TSwitchColon::class,
                    TEndswitch::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
