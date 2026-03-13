<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TRelativeNameTest extends TTestCase
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
                namespace App;
                $foo = new namespace\Bar();
                CODE,
                [
                    TPhpOpeningTag::class,
                    TNamespace::class,
                    TUnqualifiedName::class,
                    TNamespaceEndSemicolon::class,
                    TVariable::class,
                    TAssign::class,
                    TNew::class,
                    TRelativeName::class,
                    TArgsOpeningParen::class,
                    TArgsClosingParen::class,
                    TSemicolon::class,
                ],
                [
                ],
            ],
        ];
    }
}
