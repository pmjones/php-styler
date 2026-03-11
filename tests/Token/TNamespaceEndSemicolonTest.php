<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TNamespaceEndSemicolonTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'namespace-semicolon' => [
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
            ],
        ];
    }
}
