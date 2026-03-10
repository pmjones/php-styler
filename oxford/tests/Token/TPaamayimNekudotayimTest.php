<?php
declare(strict_types=1);

namespace Oxford\Token;

class TPaamayimNekudotayimTest extends TTestCase
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
                Foo::bar();
                CODE,
                [
                    TPhpOpeningTag::class,
                    TUnqualifiedName::class,
                    TMemberDoubleColon::class,
                    TStaticMethodCallName::class,
                    TArgsOpeningParen::class,
                    TArgsClosingParen::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
