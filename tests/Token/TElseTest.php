<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TElseTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'basic' => [
                <<<'CODE'
                <?php
                if ($foo == 88) {
                    $bar = 88;
                } else {
                    $bar = 79;
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TIf::class,
                    TIfOpeningParen::class,
                    TVariable::class,
                    TIsEqual::class,
                    TIntegerLiteral::class,
                    TIfClosingParen::class,
                    TIfOpeningBrace::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                    TIfContinuationBrace::class,
                    TElse::class,
                    TElseOpeningBrace::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                    TElseClosingBrace::class,
                ],
            ],
            'alternative' => [
                <<<'CODE'
                <?php
                if ($foo == 88):
                    $bar = 88;
                else:
                    $bar = 79;
                endif;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TIf::class,
                    TIfOpeningParen::class,
                    TVariable::class,
                    TIsEqual::class,
                    TIntegerLiteral::class,
                    TIfClosingParen::class,
                    TIfColon::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                    TElse::class,
                    TElseColon::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                    TEndif::class,
                    TSemicolon::class,
                ],
            ],
            'unbraced' => [
                <<<'CODE'
                <?php
                if ($foo == 88) $bar = 88;
                else $bar = 79;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TIf::class,
                    TIfOpeningParen::class,
                    TVariable::class,
                    TIsEqual::class,
                    TIntegerLiteral::class,
                    TIfClosingParen::class,
                    TOpeningBraceless::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                    TIfContinuationBraceless::class,
                    TElse::class,
                    TOpeningBraceless::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                    TElseClosingBraceless::class,
                ],
            ],
            'else-space-if-braced' => [
                <<<'CODE'
                <?php
                if ($foo == 88) {
                    $bar = 88;
                } else if ($foo == 79) {
                    $bar = 79;
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TIf::class,
                    TIfOpeningParen::class,
                    TVariable::class,
                    TIsEqual::class,
                    TIntegerLiteral::class,
                    TIfClosingParen::class,
                    TIfOpeningBrace::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                    TIfContinuationBrace::class,
                    TElse::class,
                    TIf::class,
                    TIfOpeningParen::class,
                    TVariable::class,
                    TIsEqual::class,
                    TIntegerLiteral::class,
                    TIfClosingParen::class,
                    TIfOpeningBrace::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                    TIfClosingBrace::class,
                ],
            ],
            'else-space-if-unbraced' => [
                <<<'CODE'
                <?php
                if ($foo == 88) $bar = 88;
                else if ($foo == 79) $bar = 79;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TIf::class,
                    TIfOpeningParen::class,
                    TVariable::class,
                    TIsEqual::class,
                    TIntegerLiteral::class,
                    TIfClosingParen::class,
                    TOpeningBraceless::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                    TIfContinuationBraceless::class,
                    TElse::class,
                    TIf::class,
                    TIfOpeningParen::class,
                    TVariable::class,
                    TIsEqual::class,
                    TIntegerLiteral::class,
                    TIfClosingParen::class,
                    TOpeningBraceless::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                    TIfClosingBraceless::class,
                ],
            ],
            'braced-elseif' => [
                <<<'CODE'
                <?php
                if ($foo == 88) {
                    $bar = 88;
                } elseif ($foo == 79) {
                    $bar = 79;
                } else {
                    $bar = 70;
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TIf::class,
                    TIfOpeningParen::class,
                    TVariable::class,
                    TIsEqual::class,
                    TIntegerLiteral::class,
                    TIfClosingParen::class,
                    TIfOpeningBrace::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                    TIfContinuationBrace::class,
                    TElseif::class,
                    TElseifOpeningParen::class,
                    TVariable::class,
                    TIsEqual::class,
                    TIntegerLiteral::class,
                    TElseifClosingParen::class,
                    TElseifOpeningBrace::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                    TElseifContinuationBrace::class,
                    TElse::class,
                    TElseOpeningBrace::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                    TElseClosingBrace::class,
                ],
            ],
            'alternative-elseif' => [
                <<<'CODE'
                <?php
                if ($foo == 88):
                    $bar = 88;
                elseif ($foo == 79):
                    $bar = 79;
                else:
                    $bar = 70;
                endif;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TIf::class,
                    TIfOpeningParen::class,
                    TVariable::class,
                    TIsEqual::class,
                    TIntegerLiteral::class,
                    TIfClosingParen::class,
                    TIfColon::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                    TElseif::class,
                    TElseifOpeningParen::class,
                    TVariable::class,
                    TIsEqual::class,
                    TIntegerLiteral::class,
                    TElseifClosingParen::class,
                    TElseifColon::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                    TElse::class,
                    TElseColon::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                    TEndif::class,
                    TSemicolon::class,
                ],
            ],
            'unbraced-elseif' => [
                <<<'CODE'
                <?php
                if ($foo == 88) $bar = 88;
                elseif ($foo == 79) $bar = 79;
                else $bar = 70;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TIf::class,
                    TIfOpeningParen::class,
                    TVariable::class,
                    TIsEqual::class,
                    TIntegerLiteral::class,
                    TIfClosingParen::class,
                    TOpeningBraceless::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                    TIfContinuationBraceless::class,
                    TElseif::class,
                    TElseifOpeningParen::class,
                    TVariable::class,
                    TIsEqual::class,
                    TIntegerLiteral::class,
                    TElseifClosingParen::class,
                    TOpeningBraceless::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                    TElseifContinuationBraceless::class,
                    TElse::class,
                    TOpeningBraceless::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                    TElseClosingBraceless::class,
                ],
            ],
        ];
    }
}
