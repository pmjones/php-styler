<?php
declare(strict_types=1);

namespace Oxford\Token;

class TAmpersandFollowedByVarOrVarargTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'pass-by-reference' => [
                <<<'CODE'
                <?php
                function foo(&$bar)
                {
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TFunction::class,
                    TFunctionName::class,
                    TParamsOpeningParen::class,
                    TReference::class,
                    TVariable::class,
                    TParamsClosingParen::class,
                    TFunctionOpeningBrace::class,
                    TFunctionClosingBrace::class,
                ],
            ],
        ];
    }
}
