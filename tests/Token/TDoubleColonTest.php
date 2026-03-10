<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TDoubleColonTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'static-method-call' => [
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
