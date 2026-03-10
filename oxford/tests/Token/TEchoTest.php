<?php
declare(strict_types=1);

namespace Oxford\Token;

class TEchoTest extends TTestCase
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
                echo $foo;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TEcho::class,
                    TVariable::class,
                    TEchoEndSemicolon::class,
                ],
            ],
            'parens' => [
                <<<'CODE'
                <?php
                echo ($foo);
                CODE,
                [
                    TPhpOpeningTag::class,
                    TEcho::class,
                    TExpressionOpeningParen::class,
                    TVariable::class,
                    TExpressionClosingParen::class,
                    TEchoEndSemicolon::class,
                ],
            ],
        ];
    }
}
