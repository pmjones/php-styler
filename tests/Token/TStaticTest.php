<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TStaticTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'method' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    static function bar()
                    {
                    }
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TClass::class,
                    TClassName::class,
                    TClassOpeningBrace::class,
                    TPublic::class,
                    TStatic::class,
                    TFunction::class,
                    TFunctionName::class,
                    TParamsOpeningParen::class,
                    TParamsClosingParen::class,
                    TFunctionOpeningBrace::class,
                    TFunctionClosingBrace::class,
                    TClassClosingBrace::class,
                ],
            ],
            'anonymous-function' => [
                <<<'CODE'
                <?php
                $foo = static function () {
                };
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TStatic::class,
                    TAnonymousFunction::class,
                    TParamsOpeningParen::class,
                    TParamsClosingParen::class,
                    TAnonymousOpeningBrace::class,
                    TAnonymousClosingBrace::class,
                    TSemicolon::class,
                ],
            ],
            'fn' => [
                <<<'CODE'
                <?php
                $foo = static fn () => 1;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TStatic::class,
                    TFn::class,
                    TParamsOpeningParen::class,
                    TParamsClosingParen::class,
                    TFnDoubleArrow::class,
                    TIntegerLiteral::class,
                    TFnEndSemicolon::class,
                ],
            ],
            'local-variable' => [
                <<<'CODE'
                <?php
                function foo()
                {
                    static $bar;
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TFunction::class,
                    TFunctionName::class,
                    TParamsOpeningParen::class,
                    TParamsClosingParen::class,
                    TFunctionOpeningBrace::class,
                    TStaticVar::class,
                    TVariable::class,
                    TStaticVarEndSemicolon::class,
                    TFunctionClosingBrace::class,
                ],
            ],
            'class-reference' => [
                <<<'CODE'
                <?php
                static::class;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TStaticBinding::class,
                    TMemberDoubleColon::class,
                    TStaticMemberName::class,
                    TSemicolon::class,
                ],
            ],
            'return-typehint' => [
                <<<'CODE'
                <?php
                function foo() : static
                {
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TFunction::class,
                    TFunctionName::class,
                    TParamsOpeningParen::class,
                    TParamsClosingParen::class,
                    TReturnColon::class,
                    TStaticType::class,
                    TFunctionOpeningBrace::class,
                    TFunctionClosingBrace::class,
                ],
            ],
        ];
    }
}
