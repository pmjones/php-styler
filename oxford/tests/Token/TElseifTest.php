<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TElseifTest extends TTestCase
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
                if ($foo == 88) {
                    $bar = 88;
                } elseif ($foo == 79) {
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
                    TElseifClosingBrace::class,
                ],
            ],
            'multiple' => [
                <<<'CODE'
                <?php
                if ($foo == 88) {
                    $bar = 88;
                } elseif ($foo == 79) {
                    $bar = 79;
                } elseif ($foo == 70) {
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
                    TElseifClosingBrace::class,
                ],
            ],
            'alternative' => [
                <<<'CODE'
                <?php
                if ($foo == 88):
                    $bar = 88;
                elseif ($foo == 79):
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
                    TEndif::class,
                    TSemicolon::class,
                ],
            ],
            'unbraced' => [
                <<<'CODE'
                <?php
                if ($foo == 88) $bar = 88;
                elseif ($foo == 79) $bar = 79;
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
                    TElseifClosingBraceless::class,
                ],
            ],
        ];
    }
}
