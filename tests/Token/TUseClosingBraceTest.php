<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TUseClosingBraceTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'basic' => [
                <<<'CODE'
                <?php
                use Foo\{Bar, Baz};
                CODE,
                [
                    TPhpOpeningTag::class,
                    TUse::class,
                    TUnqualifiedName::class,
                    TNamespaceSeparator::class,
                    TUseOpeningBrace::class,
                    TUnqualifiedName::class,
                    TUseComma::class,
                    TUnqualifiedName::class,
                    TUseClosingBrace::class,
                    TUseEndSemicolon::class,
                ],
            ],
        ];
    }
}
