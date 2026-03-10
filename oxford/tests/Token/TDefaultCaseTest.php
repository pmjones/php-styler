<?php
declare(strict_types=1);

namespace Oxford\Token;

class TDefaultCaseTest extends TTestCase
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
                switch ($foo) {
                    default:
                        break;
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TSwitch::class,
                    TSwitchOpeningParen::class,
                    TVariable::class,
                    TSwitchClosingParen::class,
                    TSwitchOpeningBrace::class,
                    TDefaultCase::class,
                    TCaseColon::class,
                    TBreak::class,
                    TSemicolon::class,
                    TSwitchAfterCaseClosingBrace::class,
                ],
            ],
        ];
    }
}
