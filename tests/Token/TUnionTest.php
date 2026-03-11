<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TUnionTest extends TTestCase
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
        ];
    }
}
