<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TBooleanAndTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'boolean-and' => [
                <<<'CODE'
                <?php
                $foo = true && false;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TTrue::class,
                    TBooleanAnd::class,
                    TFalse::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
