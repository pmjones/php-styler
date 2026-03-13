<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TPostDecrementTest extends TTestCase
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
                $foo--;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TPostDecrement::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
