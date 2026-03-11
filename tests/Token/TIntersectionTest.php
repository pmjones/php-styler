<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TIntersectionTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
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
            'qualified' => [
                <<<'CODE'
                <?php
                function foo(Foo\Bar&Baz\Dib $qux)
                {
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TFunction::class,
                    TFunctionName::class,
                    TParamsOpeningParen::class,
                    TQualifiedName::class,
                    TIntersection::class,
                    TQualifiedName::class,
                    TVariable::class,
                    TParamsClosingParen::class,
                    TFunctionOpeningBrace::class,
                    TFunctionClosingBrace::class,
                ],
            ],
            'fully-qualified' => [
                <<<'CODE'
                <?php
                function foo(\Foo\Bar&\Baz\Dib $qux)
                {
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TFunction::class,
                    TFunctionName::class,
                    TParamsOpeningParen::class,
                    TFullyQualifiedName::class,
                    TIntersection::class,
                    TFullyQualifiedName::class,
                    TVariable::class,
                    TParamsClosingParen::class,
                    TFunctionOpeningBrace::class,
                    TFunctionClosingBrace::class,
                ],
            ],
        ];
    }
}
