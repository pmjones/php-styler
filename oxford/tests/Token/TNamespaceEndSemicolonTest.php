<?php
declare(strict_types=1);

namespace Oxford\Token;

class TNamespaceEndSemicolonTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
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
