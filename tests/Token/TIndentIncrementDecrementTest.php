<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TIndentIncrementDecrementTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'class-body' => [
                <<<'CODE'
                <?php
                class Foo {
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TLineBreak::class,
                    TClass::class,
                    TClassName::class,
                    TLineBreak::class,
                    TClassOpeningBrace::class,
                    TLineBreak::class,
                    TIndentIncrement::class,
                    TIndentDecrement::class,
                    TClassClosingBrace::class,
                    TLineBreak::class,
                    TLineBreak::class,
                ],
                [],
                self::REPORT_SYNTHETIC,
            ],
            'function-body' => [
                <<<'CODE'
                <?php
                function foo() {
                    return 1;
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
                    TReturn::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                    TLineBreak::class,
                    TIndentDecrement::class,
                    TFunctionClosingBrace::class,
                    TLineBreak::class,
                    TLineBreak::class,
                ],
                [],
                self::REPORT_SYNTHETIC,
            ],
        ];
    }
}
