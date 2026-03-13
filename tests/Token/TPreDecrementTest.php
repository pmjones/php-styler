<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TPreDecrementTest extends TTestCase
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
