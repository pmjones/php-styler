<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TThrowTest extends TTestCase
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
                throw new Foo();
                CODE,
                [
                    TPhpOpeningTag::class,
                    TThrow::class,
                    TNew::class,
                    TUnqualifiedName::class,
                    TArgsOpeningParen::class,
                    TArgsClosingParen::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
