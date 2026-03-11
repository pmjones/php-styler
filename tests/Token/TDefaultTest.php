<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TDefaultTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'switch-default' => [
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
            'switch-case-default' => [
                <<<'CODE'
                <?php
                switch ($foo) {
                    case 1:
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
                    TDefaultAfterCase::class,
                    TCaseColon::class,
                    TBreak::class,
                    TSemicolon::class,
                    TSwitchAfterCaseClosingBrace::class,
                ],
            ],
            'match-default' => [
                <<<'CODE'
                <?php
                $foo = match ($bar) {
                    default => 'baz',
                };
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TMatch::class,
                    TMatchOpeningParen::class,
                    TVariable::class,
                    TMatchClosingParen::class,
                    TMatchOpeningBrace::class,
                    TDefaultMatch::class,
                    TMatchDoubleArrow::class,
                    TStringLiteral::class,
                    TMatchReturnComma::class,
                    TMatchClosingBrace::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
