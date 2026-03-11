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
                    TCommentSlashedOwnLine::class,
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
                    TCommentStarredOneline::class,
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
                    TCommentHashedOwnLine::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
