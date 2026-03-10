<?php
declare(strict_types=1);

namespace Oxford\Token;

class TSpreadEllipsisTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'spread-in-args' => [
                <<<'CODE'
                <?php
                foo(...$bar);
                CODE,
                [
                    TPhpOpeningTag::class,
                    TFunctionCallName::class,
                    TArgsOpeningParen::class,
                    TSpreadEllipsis::class,
                    TVariable::class,
                    TArgsClosingParen::class,
                    TSemicolon::class,
                ],
            ],
            'spread-in-array' => [
                <<<'CODE'
                <?php
                $foo = [...$bar];
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TArrayOpeningBracket::class,
                    TSpreadEllipsis::class,
                    TVariable::class,
                    TArrayClosingBracket::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
