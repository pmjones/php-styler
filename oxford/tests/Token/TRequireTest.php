<?php
declare(strict_types=1);

namespace Oxford\Token;

class TRequireTest extends TTestCase
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
                require $file;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TRequire::class,
                    TVariable::class,
                    TSemicolon::class,
                ],
            ],
            'parens' => [
                <<<'CODE'
                <?php
                require ($file);
                CODE,
                [
                    TPhpOpeningTag::class,
                    TRequire::class,
                    TExpressionOpeningParen::class,
                    TVariable::class,
                    TExpressionClosingParen::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
