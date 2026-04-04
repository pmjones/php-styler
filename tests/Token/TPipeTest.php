<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TPipeTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'function' => [
                <<<'CODE'
                <?php
                function foo() : Foo|Bar\Baz|\Zim\Dib
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
                    TUnqualifiedName::class,
                    TUnion::class,
                    TQualifiedName::class,
                    TUnion::class,
                    TFullyQualifiedName::class,
                    TFunctionOpeningBrace::class,
                    TFunctionClosingBrace::class,
                ],
            ],
            'method' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    function bar() : Foo|Bar\Baz|\Zim\Dib
                    {
                    }
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TClass::class,
                    TClassName::class,
                    TClassOpeningBrace::class,
                    TFunction::class,
                    TFunctionName::class,
                    TParamsOpeningParen::class,
                    TParamsClosingParen::class,
                    TReturnColon::class,
                    TUnqualifiedName::class,
                    TUnion::class,
                    TQualifiedName::class,
                    TUnion::class,
                    TFullyQualifiedName::class,
                    TFunctionOpeningBrace::class,
                    TFunctionClosingBrace::class,
                    TClassClosingBrace::class,
                ],
            ],
            'bitwise-or' => [
                <<<'CODE'
                <?php
                $foo = 1 | 2;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TBitwiseOr::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
