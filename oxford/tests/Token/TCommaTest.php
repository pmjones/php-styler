<?php
declare(strict_types=1);

namespace Oxford\Token;

class TCommaTest extends TTestCase
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
                foo($bar, $baz);
                CODE,
                [
                    TPhpOpeningTag::class,
                    TFunctionCallName::class,
                    TArgsOpeningParen::class,
                    TVariable::class,
                    TArgsComma::class,
                    TVariable::class,
                    TArgsClosingParen::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
