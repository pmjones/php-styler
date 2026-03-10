<?php
declare(strict_types=1);

namespace Oxford\Token;

class TDocCommentOnelineTest extends TTestCase
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
