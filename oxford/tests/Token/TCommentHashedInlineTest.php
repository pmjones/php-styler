<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TCommentHashedInlineTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'mid-expression' => [
                <<<'CODE'
                <?php
                $foo = 1 # mid
                    + 2;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TCommentHashedMidStatement::class,
                    TBinaryPlus::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                ],
            ],
            'after-comma' => [
                <<<'CODE'
                <?php
                foo(
                    $a, # mid
                    $b
                );
                CODE,
                [
                    TPhpOpeningTag::class,
                    TFunctionCallName::class,
                    TArgsOpeningParen::class,
                    TVariable::class,
                    TArgsComma::class,
                    TCommentHashedMidStatement::class,
                    TVariable::class,
                    TArgsClosingParen::class,
                    TSemicolon::class,
                ],
            ],
            'after-semicolon' => [
                <<<'CODE'
                <?php
                $foo = 1; # foo
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                    TCommentHashedInline::class,
                ],
            ],
            'after-open-tag' => [
                <<<'CODE'
                <?php # foo
                CODE,
                [
                    TPhpOpeningTagInline::class,
                    TCommentHashedInline::class,
                ],
            ],
        ];
    }
}
