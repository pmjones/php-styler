<?php
declare(strict_types=1);

namespace Oxford\Token;

class TNamespaceSeparatorTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
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
