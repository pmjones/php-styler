<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TLineBreakTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'after-semicolon' => [
                <<<'CODE'
                <?php
                $foo = 1;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TLineBreak::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                    TLineBreak::class,
                ],
                [],
                self::REPORT_SYNTHETIC,
            ],
            'after-opening-brace' => [
                <<<'CODE'
                <?php
                function foo() {
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TLineBreak::class,
                    TFunction::class,
                    TFunctionName::class,
                    TParamsOpeningParen::class,
                    TParamsClosingParen::class,
                    TLineBreak::class,
                    TFunctionOpeningBrace::class,
                    TLineBreak::class,
                    TIndentIncrement::class,
                    TIndentDecrement::class,
                    TFunctionClosingBrace::class,
                    TLineBreak::class,
                    TLineBreak::class,
                ],
                [],
                self::REPORT_SYNTHETIC,
            ],
            'own-line-comment' => [
                <<<'CODE'
                <?php
                $foo = 1;
                // comment
                $bar = 2;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TLineBreak::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                    TLineBreak::class,
                    TCommentSlashed::class,
                    TLineBreak::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                    TLineBreak::class,
                ],
                [],
                self::REPORT_SYNTHETIC,
            ],
        ];
    }
}
