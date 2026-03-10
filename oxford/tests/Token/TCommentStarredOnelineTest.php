<?php
declare(strict_types=1);

namespace Oxford\Token;

class TCommentStarredOnelineTest extends TTestCase
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
                $foo = 1;
                /* bar */
                $bar = 1;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                    TCommentStarredOneline::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                ],
            ],
            'open-tag' => [
                <<<'CODE'
                <?php
                /* foo */
                $foo = 1;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TCommentStarredOneline::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
