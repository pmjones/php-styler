<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TDocCommentLineBreakTest extends TTestCase
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
                /** @var int */
                $foo = 1;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TDocComment::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
