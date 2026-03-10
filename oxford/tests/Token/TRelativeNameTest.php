<?php
declare(strict_types=1);

namespace Oxford\Token;

class TRelativeNameTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
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
                [],
            ],
        ];
    }
}
