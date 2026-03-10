<?php
declare(strict_types=1);

namespace Oxford\Token;

class TArgsOpeningParenTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'function' => [
                <<<'CODE'
                <?php
                foo();
                CODE,
                [
                    TPhpOpeningTag::class,
                    TFunctionCallName::class,
                    TArgsOpeningParen::class,
                    TArgsClosingParen::class,
                    TSemicolon::class,
                ],
            ],
            'qualified-function' => [
                <<<'CODE'
                <?php
                Foo\bar();
                CODE,
                [
                    TPhpOpeningTag::class,
                    TFunctionCallQualified::class,
                    TArgsOpeningParen::class,
                    TArgsClosingParen::class,
                    TSemicolon::class,
                ],
            ],
            'fully-qualified-function' => [
                <<<'CODE'
                <?php
                \Foo\Bar\baz();
                CODE,
                [
                    TPhpOpeningTag::class,
                    TFunctionCallFullyQualified::class,
                    TArgsOpeningParen::class,
                    TArgsClosingParen::class,
                    TSemicolon::class,
                ],
            ],
            'variable' => [
                <<<'CODE'
                <?php
                $a();
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TArgsOpeningParen::class,
                    TArgsClosingParen::class,
                    TSemicolon::class,
                ],
            ],
            'instance-method' => [
                <<<'CODE'
                <?php
                $this->foo($i);
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TObjectOperator::class,
                    TMethodCallName::class,
                    TArgsOpeningParen::class,
                    TVariable::class,
                    TArgsClosingParen::class,
                    TSemicolon::class,
                ],
            ],
            'instance-method-braced' => [
                <<<'CODE'
                <?php
                $this->{$a}();
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TObjectOperator::class,
                    TDynamicMemberOpeningBrace::class,
                    TVariable::class,
                    TDynamicMemberClosingBrace::class,
                    TArgsOpeningParen::class,
                    TArgsClosingParen::class,
                    TSemicolon::class,
                ],
            ],
            'instance-method-concat' => [
                <<<'CODE'
                <?php
                $this->{$a . $b}();
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TObjectOperator::class,
                    TDynamicMemberOpeningBrace::class,
                    TVariable::class,
                    TDot::class,
                    TVariable::class,
                    TDynamicMemberClosingBrace::class,
                    TArgsOpeningParen::class,
                    TArgsClosingParen::class,
                    TSemicolon::class,
                ],
            ],
            'instance-method-nullsafe' => [
                <<<'CODE'
                <?php
                $this?->foo($i, $j);
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TNullsafeObjectOperator::class,
                    TMethodCallName::class,
                    TArgsOpeningParen::class,
                    TVariable::class,
                    TArgsComma::class,
                    TVariable::class,
                    TArgsClosingParen::class,
                    TSemicolon::class,
                ],
            ],
            'static-method' => [
                <<<'CODE'
                <?php
                self::foo();
                CODE,
                [
                    TPhpOpeningTag::class,
                    TSelf::class,
                    TMemberDoubleColon::class,
                    TStaticMethodCallName::class,
                    TArgsOpeningParen::class,
                    TArgsClosingParen::class,
                    TSemicolon::class,
                ],
            ],
            'static-method-variable' => [
                <<<'CODE'
                <?php
                self::$a();
                CODE,
                [
                    TPhpOpeningTag::class,
                    TSelf::class,
                    TMemberDoubleColon::class,
                    TVariable::class,
                    TArgsOpeningParen::class,
                    TArgsClosingParen::class,
                    TSemicolon::class,
                ],
            ],
            'static-method-concat' => [
                <<<'CODE'
                <?php
                self::{$a . $b}();
                CODE,
                [
                    TPhpOpeningTag::class,
                    TSelf::class,
                    TMemberDoubleColon::class,
                    TDynamicMemberOpeningBrace::class,
                    TVariable::class,
                    TDot::class,
                    TVariable::class,
                    TDynamicMemberClosingBrace::class,
                    TArgsOpeningParen::class,
                    TArgsClosingParen::class,
                    TSemicolon::class,
                ],
            ],
            'new' => [
                <<<'CODE'
                <?php
                new Foo();
                CODE,
                [
                    TPhpOpeningTag::class,
                    TNew::class,
                    TUnqualifiedName::class,
                    TArgsOpeningParen::class,
                    TArgsClosingParen::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
