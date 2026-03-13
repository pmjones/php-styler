<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TPreIncrementTest extends TTestCase
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
