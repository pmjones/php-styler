<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TMethodCallNameTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'instance-method' => [
                <<<'CODE'
                <?php
                $this->foo();
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TObjectOperator::class,
                    TMethodCallName::class,
                    TArgsOpeningParen::class,
                    TArgsClosingParen::class,
                    TSemicolon::class,
                ],
            ],
            'nullsafe-method' => [
                <<<'CODE'
                <?php
                $this?->foo();
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TNullsafeObjectOperator::class,
                    TMethodCallName::class,
                    TArgsOpeningParen::class,
                    TArgsClosingParen::class,
                    TSemicolon::class,
                ],
            ],
            'chained' => [
                <<<'CODE'
                <?php
                $this->foo()->bar();
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TObjectOperator::class,
                    TMethodCallName::class,
                    TArgsOpeningParen::class,
                    TArgsClosingParen::class,
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
