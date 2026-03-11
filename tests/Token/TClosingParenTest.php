<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TClosingParenTest extends TTestCase
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
