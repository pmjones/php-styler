<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TAmpersandNotFollowedByVarOrVarargTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'bitwise-and' => [
                <<<'CODE'
                <?php
                $foo = 1 & 2;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TBitwiseAnd::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                ],
            ],
            'intersection-type' => [
                <<<'CODE'
                <?php
                function foo(Foo&Bar $baz)
                {
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TFunction::class,
                    TFunctionName::class,
                    TParamsOpeningParen::class,
                    TUnqualifiedName::class,
                    TIntersection::class,
                    TUnqualifiedName::class,
                    TVariable::class,
                    TParamsClosingParen::class,
                    TFunctionOpeningBrace::class,
                    TFunctionClosingBrace::class,
                ],
            ],
            'bitwise-and-of-constants' => [
                <<<'CODE'
                <?php
                const FLAGS = FOO & BAR;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TConst::class,
                    TConstantName::class,
                    TAssignConst::class,
                    TUnknownString::class,
                    TBitwiseAnd::class,
                    TUnknownString::class,
                    TNamespaceConstEndSemicolon::class,
                ],
            ],
        ];
    }
}
