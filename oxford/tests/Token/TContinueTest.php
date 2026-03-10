<?php
declare(strict_types=1);

namespace Oxford\Token;

class TContinueTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'continue-in-while' => [
                <<<'CODE'
                <?php
                while (true) {
                    continue;
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TWhile::class,
                    TWhileOpeningParen::class,
                    TTrue::class,
                    TWhileClosingParen::class,
                    TWhileOpeningBrace::class,
                    TContinue::class,
                    TSemicolon::class,
                    TWhileClosingBrace::class,
                ],
            ],
            'continue-in-for' => [
                <<<'CODE'
                <?php
                for (;;) {
                    continue;
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TFor::class,
                    TForOpeningParen::class,
                    TLoopEmptySemicolon::class,
                    TLoopEmptySemicolon::class,
                    TLoopEmptyClosingParen::class,
                    TForOpeningBrace::class,
                    TContinue::class,
                    TSemicolon::class,
                    TForClosingBrace::class,
                ],
            ],
            'continue-in-switch' => [
                <<<'CODE'
                <?php
                switch ($foo) {
                    case 'bar':
                        continue;
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TSwitch::class,
                    TSwitchOpeningParen::class,
                    TVariable::class,
                    TSwitchClosingParen::class,
                    TSwitchOpeningBrace::class,
                    TCase::class,
                    TStringLiteral::class,
                    TCaseColon::class,
                    TContinue::class,
                    TSemicolon::class,
                    TSwitchAfterCaseClosingBrace::class,
                ],
            ],
        ];
    }
}
