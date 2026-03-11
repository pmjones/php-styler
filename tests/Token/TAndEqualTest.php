<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TAndEqualTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'and-equal' => [
                <<<'CODE'
                <?php
                $foo &= 1;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAndEqual::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
