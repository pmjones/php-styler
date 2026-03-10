<?php
declare(strict_types=1);

namespace Oxford\Token;

class TArgsClosingParenTest extends TTestCase
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
                foo($bar);
                CODE,
                [
                    TPhpOpeningTag::class,
                    TFunctionCallName::class,
                    TArgsOpeningParen::class,
                    TVariable::class,
                    TArgsClosingParen::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
