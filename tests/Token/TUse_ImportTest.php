<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TUse_ImportTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'string' => [
                <<<'CODE'
                <?php
                use Foo;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TUse::class,
                    TUnqualifiedName::class,
                    TUseEndSemicolon::class,
                ],
            ],
            'namespaced' => [
                <<<'CODE'
                <?php
                use Foo\Bar;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TUse::class,
                    TQualifiedName::class,
                    TUseEndSemicolon::class,
                ],
            ],
            'string-as' => [
                <<<'CODE'
                <?php
                use Foo as Bar;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TUse::class,
                    TUnqualifiedName::class,
                    TUseAs::class,
                    TUseAlias::class,
                    TUseEndSemicolon::class,
                ],
            ],
            'namespaced-as' => [
                <<<'CODE'
                <?php
                use Foo\Bar as Baz;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TUse::class,
                    TQualifiedName::class,
                    TUseAs::class,
                    TUseAlias::class,
                    TUseEndSemicolon::class,
                ],
            ],
            'grouped' => [
                <<<'CODE'
                <?php
                use Foo, Bar as Baz, Dib;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TUse::class,
                    TUnqualifiedName::class,
                    TUseComma::class,
                    TUnqualifiedName::class,
                    TUseAs::class,
                    TUseAlias::class,
                    TUseComma::class,
                    TUnqualifiedName::class,
                    TUseEndSemicolon::class,
                ],
            ],
            'prefixed' => [
                <<<'CODE'
                <?php
                use Foo\Bar\{Baz, Dib as Zim, Gir};
                CODE,
                [
                    TPhpOpeningTag::class,
                    TUse::class,
                    TQualifiedName::class,
                    TUseEndSemicolon::class,
                    TUse::class,
                    TQualifiedName::class,
                    TUseAs::class,
                    TUseAlias::class,
                    TUseEndSemicolon::class,
                    TUse::class,
                    TQualifiedName::class,
                    TUseEndSemicolon::class,
                ],
            ],
        ];
    }
}
