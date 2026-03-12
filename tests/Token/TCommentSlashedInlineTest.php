<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TCommentSlashedInlineTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'mid-expression' => [
                <<<'CODE'
                <?php
                $foo = 1 // mid
                    + 2;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TCommentSlashedMidStatement::class,
                    TBinaryPlus::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                ],
            ],
            'after-comma' => [
                <<<'CODE'
                <?php
                foo(
                    $a, // mid
                    $b
                );
                CODE,
                [
                    TPhpOpeningTag::class,
                    TFunctionCallName::class,
                    TArgsOpeningParen::class,
                    TVariable::class,
                    TArgsComma::class,
                    TCommentSlashedMidStatement::class,
                    TVariable::class,
                    TArgsClosingParen::class,
                    TSemicolon::class,
                ],
            ],
            'after-semicolon' => [
                <<<'CODE'
                <?php
                $foo = 1; // foo
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                    TCommentSlashedLineBreak::class,
                ],
            ],
            'after-open-tag' => [
                <<<'CODE'
                <?php // foo
                CODE,
                [
                    TPhpOpeningTagInline::class,
                    TCommentSlashedMidStatement::class,
                ],
            ],
        ];
    }
}
