<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TIsGreaterOrEqualTest extends TTestCase
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
                $foo >= $bar;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TIsGreaterOrEqual::class,
                    TVariable::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
