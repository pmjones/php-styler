<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TSwitchTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'braced-empty' => [
                <<<'CODE'
                <?php
                switch (true) {
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TSwitch::class,
                    TSwitchOpeningParen::class,
                    TTrue::class,
                    TSwitchClosingParen::class,
                    TSwitchOpeningBrace::class,
                    TSwitchClosingBrace::class,
                ],
            ],
            'alternative-empty' => [
                <<<'CODE'
                <?php
                switch (true):
                endswitch;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TSwitch::class,
                    TSwitchOpeningParen::class,
                    TTrue::class,
                    TSwitchClosingParen::class,
                    TSwitchColon::class,
                    TEndswitch::class,
                    TSemicolon::class,
                ],
            ],
            'braced' => [
                <<<'CODE'
                <?php
                switch ($foo) {
                    case 1:
                        break;

                    case 2:
                    case 3:
                        break;

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
                    TCase::class,
                    TIntegerLiteral::class,
                    TCaseColon::class,
                    TBreak::class,
                    TSemicolon::class,
                    TCaseAfterCase::class,
                    TIntegerLiteral::class,
                    TCaseFallthroughColon::class,
                    TCase::class,
                    TIntegerLiteral::class,
                    TCaseColon::class,
                    TBreak::class,
                    TSemicolon::class,
                    TDefaultAfterCase::class,
                    TCaseColon::class,
                    TBreak::class,
                    TSemicolon::class,
                    TSwitchAfterCaseClosingBrace::class,
                ],
            ],
            'alternative' => [
                <<<'CODE'
                <?php
                switch ($foo):
                    case 1:
                        break;

                    case 2:
                    case 3:
                        break;

                    default:
                        break;
                endswitch;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TSwitch::class,
                    TSwitchOpeningParen::class,
                    TVariable::class,
                    TSwitchClosingParen::class,
                    TSwitchColon::class,
                    TCase::class,
                    TIntegerLiteral::class,
                    TCaseColon::class,
                    TBreak::class,
                    TSemicolon::class,
                    TCaseAfterCase::class,
                    TIntegerLiteral::class,
                    TCaseFallthroughColon::class,
                    TCase::class,
                    TIntegerLiteral::class,
                    TCaseColon::class,
                    TBreak::class,
                    TSemicolon::class,
                    TDefaultAfterCase::class,
                    TCaseColon::class,
                    TBreak::class,
                    TSemicolon::class,
                    TEndswitchAfterCase::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
