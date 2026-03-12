<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TCommentTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'slashed' => [
                <<<'CODE'
                <?php
                // foo
                $foo = 1;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TCommentSlashed::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                ],
            ],
            'starred' => [
                <<<'CODE'
                <?php
                /* foo */
                $foo = 1;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TCommentStarred::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                ],
            ],
            'hashed' => [
                <<<'CODE'
                <?php
                # foo
                $foo = 1;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TCommentHashed::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
