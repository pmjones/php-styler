<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TDocCommentInlineTest extends TTestCase
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
                $foo /** Foo */ = 1;
                $bar /** Bar */ = 2;

                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TDocCommentInline::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                    TVariable::class,
                    TDocCommentInline::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                ],
            ],
            'mid-expression' => [
                <<<'CODE'
                <?php
                $foo = 1 /** mid */
                    + 2;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TDocCommentInline::class,
                    TBinaryPlus::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                ],
            ],
            'before-code' => [
                <<<'CODE'
                <?php
                /** Foo */ $foo = 1;
                /** Bar */ $bar = 2;

                CODE,
                [
                    TPhpOpeningTag::class,
                    TDocCommentInline::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                    TDocCommentInline::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                ],
            ],
            'after-comma' => [
                <<<'CODE'
                <?php
                foo(
                    $a, /** mid */
                    $b
                );
                CODE,
                [
                    TPhpOpeningTag::class,
                    TFunctionCallName::class,
                    TArgsOpeningParen::class,
                    TVariable::class,
                    TArgsComma::class,
                    TDocCommentInline::class,
                    TVariable::class,
                    TArgsClosingParen::class,
                    TSemicolon::class,
                ],
            ],
            'after-semicolon' => [
                <<<'CODE'
                <?php
                $foo = 1; /** Foo */
                $bar = 2; /** Bar */

                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                    TDocCommentLineBreak::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                    TDocCommentLineBreak::class,
                ],
            ],
        ];
    }
}
