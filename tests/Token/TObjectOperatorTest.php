<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TObjectOperatorTest extends TTestCase
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
                $foo->bar;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TObjectOperator::class,
                    TPropertyAccessName::class,
                    TSemicolon::class,
                ],
            ],
            'after-new' => [
                <<<'CODE'
                <?php
                (new Foo())->bar();
                CODE,
                [
                    TPhpOpeningTag::class,
                    TExpressionOpeningParen::class,
                    TNew::class,
                    TUnqualifiedName::class,
                    TArgsOpeningParen::class,
                    TArgsClosingParen::class,
                    TExpressionClosingParen::class,
                    TObjectOperator::class,
                    TMethodCallName::class,
                    TArgsOpeningParen::class,
                    TArgsClosingParen::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
