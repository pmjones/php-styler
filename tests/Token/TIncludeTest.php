<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TIncludeTest extends TTestCase
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
                include $file;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TInclude::class,
                    TVariable::class,
                    TSemicolon::class,
                ],
            ],
            'parens' => [
                <<<'CODE'
                <?php
                include ($file);
                CODE,
                [
                    TPhpOpeningTag::class,
                    TInclude::class,
                    TExpressionOpeningParen::class,
                    TVariable::class,
                    TExpressionClosingParen::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
