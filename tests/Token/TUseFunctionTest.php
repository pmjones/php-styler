<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TUseFunctionTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'global' => [
                <<<'CODE'
                <?php
                use function foo;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TUse::class,
                    TUseFunction::class,
                    TFunctionName::class,
                    TUseEndSemicolon::class,
                ],
            ],
            'namespaced' => [
                <<<'CODE'
                <?php
                use function Foo\bar;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TUse::class,
                    TUseFunction::class,
                    TQualifiedName::class,
                    TUseEndSemicolon::class,
                ],
            ],
            'string-as' => [
                <<<'CODE'
                <?php
                use function foo as bar;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TUse::class,
                    TUseFunction::class,
                    TFunctionName::class,
                    TUseAs::class,
                    TFunctionName::class,
                    TUseEndSemicolon::class,
                ],
            ],
            'namespaced-as' => [
                <<<'CODE'
                <?php
                use function Foo\bar as baz;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TUse::class,
                    TUseFunction::class,
                    TQualifiedName::class,
                    TUseAs::class,
                    TFunctionName::class,
                    TUseEndSemicolon::class,
                ],
            ],
            'grouped' => [
                <<<'CODE'
                <?php
                use function foo, bar as baz, dib;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TUse::class,
                    TUseFunction::class,
                    TFunctionName::class,
                    TUseComma::class,
                    TFunctionName::class,
                    TUseAs::class,
                    TFunctionName::class,
                    TUseComma::class,
                    TFunctionName::class,
                    TUseEndSemicolon::class,
                ],
            ],
            'prefixed' => [
                <<<'CODE'
                <?php
                use function Foo\Bar\{baz, dib as zim, gir};
                CODE,
                [
                    TPhpOpeningTag::class,
                    TUse::class,
                    TUseFunction::class,
                    TQualifiedName::class,
                    TUseEndSemicolon::class,
                    TUse::class,
                    TUseFunction::class,
                    TQualifiedName::class,
                    TUseAs::class,
                    TFunctionName::class,
                    TUseEndSemicolon::class,
                    TUse::class,
                    TUseFunction::class,
                    TQualifiedName::class,
                    TUseEndSemicolon::class,
                ],
            ],
        ];
    }
}
