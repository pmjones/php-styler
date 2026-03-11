<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TFnEndSemicolonTest extends TTestCase
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
                $foo = fn($x) => $x + 1;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TFn::class,
                    TParamsOpeningParen::class,
                    TVariable::class,
                    TParamsClosingParen::class,
                    TFnDoubleArrow::class,
                    TVariable::class,
                    TBinaryPlus::class,
                    TIntegerLiteral::class,
                    TFnEndSemicolon::class,
                ],
            ],
        ];
    }
}
