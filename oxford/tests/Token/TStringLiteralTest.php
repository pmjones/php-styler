<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TStringLiteralTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'single-quoted' => [
                <<<'CODE'
                <?php
                $foo = 'bar';
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TStringLiteral::class,
                    TSemicolon::class,
                ],
            ],
            'double-quoted' => [
                <<<'CODE'
                <?php
                $foo = "bar";
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TStringLiteral::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
