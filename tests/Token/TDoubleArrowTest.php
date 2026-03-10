<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TDoubleArrowTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'array-pair' => [
                <<<'CODE'
                <?php
                $foo = ['bar' => 'baz'];
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TArrayOpeningBracket::class,
                    TStringLiteral::class,
                    TArrayDoubleArrow::class,
                    TStringLiteral::class,
                    TArrayClosingBracket::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
