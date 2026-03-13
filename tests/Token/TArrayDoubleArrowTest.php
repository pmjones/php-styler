<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TArrayDoubleArrowTest extends TTestCase
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
                $foo = ['a' => 1];
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TArrayOpeningBracket::class,
                    TStringLiteral::class,
                    TArrayDoubleArrow::class,
                    TIntegerLiteral::class,
                    TArrayClosingBracket::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
