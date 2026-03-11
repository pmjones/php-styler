<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TRequireOnceTest extends TTestCase
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
                require_once $file;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TRequireOnce::class,
                    TVariable::class,
                    TSemicolon::class,
                ],
            ],
            'parens' => [
                <<<'CODE'
                <?php
                require_once ($file);
                CODE,
                [
                    TPhpOpeningTag::class,
                    TRequireOnce::class,
                    TExpressionOpeningParen::class,
                    TVariable::class,
                    TExpressionClosingParen::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
