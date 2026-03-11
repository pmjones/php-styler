<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TCommentStarredInlineTest extends TTestCase
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
                $foo /* bar */ = 1;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TCommentStarredInline::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                ],
            ],
            'mid-expression' => [
                <<<'CODE'
                <?php
                $foo = 1 /* mid */
                    + 2;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TCommentStarredInline::class,
                    TBinaryPlus::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                ],
            ],
            'before-code' => [
                <<<'CODE'
                <?php
                /* bar */ $foo = 1;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TCommentStarredInline::class,
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
                    $a, /* mid */
                    $b
                );
                CODE,
                [
                    TPhpOpeningTag::class,
                    TFunctionCallName::class,
                    TArgsOpeningParen::class,
                    TVariable::class,
                    TArgsComma::class,
                    TCommentStarredInline::class,
                    TVariable::class,
                    TArgsClosingParen::class,
                    TSemicolon::class,
                ],
            ],
            'after-semicolon' => [
                <<<'CODE'
                <?php
                $foo = 1; /* bar */

                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                    TCommentStarredInline::class,
                ],
            ],
        ];
    }
}
