<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TPowerEqualTest extends TTestCase
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
                $foo **= 2;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TPowerEqual::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
