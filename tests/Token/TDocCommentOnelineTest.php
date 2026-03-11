<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TDocCommentOnelineTest extends TTestCase
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
                /** Foo */
                $foo = 1;

                /** Bar */
                $bar = 2;

                CODE,
                [
                    TPhpOpeningTag::class,
                    TDocCommentOneline::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                    TDocCommentOneline::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
