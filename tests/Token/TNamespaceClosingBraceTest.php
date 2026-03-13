<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TNamespaceClosingBraceTest extends TTestCase
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
                namespace Foo {}
                CODE,
                [
                    TPhpOpeningTag::class,
                    TNamespace::class,
                    TUnqualifiedName::class,
                    TNamespaceOpeningBrace::class,
                    TNamespaceClosingBrace::class,
                ],
            ],
        ];
    }
}
