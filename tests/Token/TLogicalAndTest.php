<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TLogicalAndTest extends TTestCase
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
                $foo and $bar;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TLogicalAnd::class,
                    TVariable::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
