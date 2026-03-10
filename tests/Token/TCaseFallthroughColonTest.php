<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TCaseFallthroughColonTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'case-to-case' => [
                <<<'CODE'
                <?php
                switch ($foo) {
                    case 1:
                    case 2:
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
                    TCaseFallthroughColon::class,
                    TCase::class,
                    TIntegerLiteral::class,
                    TCaseColon::class,
                    TBreak::class,
                    TSemicolon::class,
                    TSwitchAfterCaseClosingBrace::class,
                ],
            ],
            'case-to-default' => [
                <<<'CODE'
                <?php
                switch ($foo) {
                    case 1:
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
                    TCaseFallthroughColon::class,
                    TDefaultCase::class,
                    TCaseColon::class,
                    TBreak::class,
                    TSemicolon::class,
                    TSwitchAfterCaseClosingBrace::class,
                ],
            ],
            'default-to-case' => [
                <<<'CODE'
                <?php
                switch ($foo) {
                    default:
                    case 1:
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
                    TCaseFallthroughColon::class,
                    TCase::class,
                    TIntegerLiteral::class,
                    TCaseColon::class,
                    TBreak::class,
                    TSemicolon::class,
                    TSwitchAfterCaseClosingBrace::class,
                ],
            ],
            'multiple-fallthrough' => [
                <<<'CODE'
                <?php
                switch ($foo) {
                    case 1:
                    case 2:
                    case 3:
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
                    TCaseFallthroughColon::class,
                    TCase::class,
                    TIntegerLiteral::class,
                    TCaseFallthroughColon::class,
                    TCase::class,
                    TIntegerLiteral::class,
                    TCaseColon::class,
                    TBreak::class,
                    TSemicolon::class,
                    TSwitchAfterCaseClosingBrace::class,
                ],
            ],
        ];
    }
}
