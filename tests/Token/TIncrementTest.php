<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TIncrementTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'post-increment' => [
                <<<'CODE'
                <?php
                $foo++;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TPostIncrement::class,
                    TSemicolon::class,
                ],
            ],
            'pre-increment' => [
                <<<'CODE'
                <?php
                ++$foo;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TPreIncrement::class,
                    TVariable::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
