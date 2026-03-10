<?php
declare(strict_types=1);

namespace Oxford\Token;

class TClosingParenTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'basic' => [
                <<<'CODE'
                <?php
                $foo = ($bar);
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TExpressionOpeningParen::class,
                    TVariable::class,
                    TExpressionClosingParen::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
