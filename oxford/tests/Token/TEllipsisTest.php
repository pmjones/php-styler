<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TEllipsisTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'variadic-param' => [
                <<<'CODE'
                <?php
                function foo(...$bar) {}
                CODE,
                [
                    TPhpOpeningTag::class,
                    TFunction::class,
                    TFunctionName::class,
                    TParamsOpeningParen::class,
                    TVariadicEllipsis::class,
                    TVariable::class,
                    TParamsClosingParen::class,
                    TFunctionOpeningBrace::class,
                    TFunctionClosingBrace::class,
                ],
            ],
            'first-class-callable' => [
                <<<'CODE'
                <?php
                $foo = strlen(...);
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TFunctionCallName::class,
                    TArgsOpeningParen::class,
                    TFirstClassCallableEllipsis::class,
                    TArgsClosingParen::class,
                    TSemicolon::class,
                ],
            ],
            'first-class-callable-method' => [
                <<<'CODE'
                <?php
                $foo = $obj->method(...);
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TVariable::class,
                    TObjectOperator::class,
                    TMethodCallName::class,
                    TArgsOpeningParen::class,
                    TFirstClassCallableEllipsis::class,
                    TArgsClosingParen::class,
                    TSemicolon::class,
                ],
            ],
            'first-class-callable-static' => [
                <<<'CODE'
                <?php
                $foo = Closure::fromCallable(...);
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TUnqualifiedName::class,
                    TMemberDoubleColon::class,
                    TStaticMethodCallName::class,
                    TArgsOpeningParen::class,
                    TFirstClassCallableEllipsis::class,
                    TArgsClosingParen::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
