<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TAssignDefaultTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'param-default' => [
                <<<'CODE'
                <?php
                function foo($bar = 1)
                {
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TFunction::class,
                    TFunctionName::class,
                    TParamsOpeningParen::class,
                    TVariable::class,
                    TAssignDefault::class,
                    TIntegerLiteral::class,
                    TParamsClosingParen::class,
                    TFunctionOpeningBrace::class,
                    TFunctionClosingBrace::class,
                ],
            ],
        ];
    }
}
