<?php
declare(strict_types=1);

namespace Oxford\Token;

class TFunctionCallQualifiedTest extends TTestCase
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
                Foo\bar();
                CODE,
                [
                    TPhpOpeningTag::class,
                    TFunctionCallQualified::class,
                    TArgsOpeningParen::class,
                    TArgsClosingParen::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
