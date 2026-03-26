<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TNamespaceSeparatorTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'group-use' => [
                <<<'CODE'
                <?php
                use Foo\Bar\{Baz, Dib};
                CODE,
                [
                    TPhpOpeningTag::class,
                    TUse::class,
                    TQualifiedName::class,
                    TUseEndSemicolon::class,
                    TUse::class,
                    TQualifiedName::class,
                    TUseEndSemicolon::class,
                ],
            ],
        ];
    }
}
