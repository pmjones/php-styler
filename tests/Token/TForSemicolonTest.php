<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TForSemicolonTest extends TTestCase
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
                for ($i = 0; $i < 10; $i ++) {
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TFor::class,
                    TForOpeningParen::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TForSemicolon::class,
                    TVariable::class,
                    TSmallerThan::class,
                    TIntegerLiteral::class,
                    TForSemicolon::class,
                    TVariable::class,
                    TPostIncrement::class,
                    TForClosingParen::class,
                    TForOpeningBrace::class,
                    TForClosingBrace::class,
                ],
            ],
        ];
    }
}
