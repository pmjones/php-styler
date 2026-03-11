<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TModuloTest extends TTestCase
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
                $foo = 10 % 3;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TModulo::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
