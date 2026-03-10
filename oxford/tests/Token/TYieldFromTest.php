<?php
declare(strict_types=1);

namespace Oxford\Token;

class TYieldFromTest extends TTestCase
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
                function foo() {
                    yield from bar();
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TFunction::class,
                    TFunctionName::class,
                    TParamsOpeningParen::class,
                    TParamsClosingParen::class,
                    TFunctionOpeningBrace::class,
                    TYieldFrom::class,
                    TFunctionCallName::class,
                    TArgsOpeningParen::class,
                    TArgsClosingParen::class,
                    TSemicolon::class,
                    TFunctionClosingBrace::class,
                ],
            ],
        ];
    }
}
