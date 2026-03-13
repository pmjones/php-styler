<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TFunctionCallRelativeTest extends TTestCase
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
                namespace\foo();
                CODE,
                [
                    TPhpOpeningTag::class,
                    TFunctionCallRelative::class,
                    TArgsOpeningParen::class,
                    TArgsClosingParen::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
