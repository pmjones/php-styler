<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TCommentHashedTest extends TTestCase
{
    /**
     * @inheritdoc
     */
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
                $foo = 1;
                # bar
                $bar = 1;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                    TCommentHashedOwnLine::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                ],
            ],
            'open-tag' => [
                <<<'CODE'
                <?php
                # foo
                CODE,
                [
                    TPhpOpeningTag::class,
                    TCommentHashedOwnLine::class,
                ],
            ],
        ];
    }
}
