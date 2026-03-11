<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TUseAliasTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'simple' => [
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
            'namespaced' => [
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
        ];
    }
}
