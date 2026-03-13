<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TNamespaceTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'minimal-string' => [
                <<<'CODE'
                <?php
                namespace Foo;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TNamespace::class,
                    TUnqualifiedName::class,
                    TNamespaceEndSemicolon::class,
                ],
                [
                ],
            ],
            'minimal-name-qualified' => [
                <<<'CODE'
                <?php
                namespace Foo\Bar;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TNamespace::class,
                    TQualifiedName::class,
                    TNamespaceEndSemicolon::class,
                ],
                [
                ],
            ],
            'braced-global' => [
                <<<'CODE'
                <?php
                namespace {
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TNamespace::class,
                    TNamespaceOpeningBrace::class,
                    TNamespaceClosingBrace::class,
                ],
            ],
            'braced-string' => [
                <<<'CODE'
                <?php
                namespace Foo {
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TNamespace::class,
                    TUnqualifiedName::class,
                    TNamespaceOpeningBrace::class,
                    TNamespaceClosingBrace::class,
                ],
            ],
            'braced-name-qualified' => [
                <<<'CODE'
                <?php
                namespace Foo\Bar {
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TNamespace::class,
                    TQualifiedName::class,
                    TNamespaceOpeningBrace::class,
                    TNamespaceClosingBrace::class,
                ],
            ],
        ];
    }
}
