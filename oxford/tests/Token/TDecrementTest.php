<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TDecrementTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'post-decrement' => [
                <<<'CODE'
                <?php
                $foo--;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TPostDecrement::class,
                    TSemicolon::class,
                ],
            ],
            'pre-decrement' => [
                <<<'CODE'
                <?php
                --$foo;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TPreDecrement::class,
                    TVariable::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
