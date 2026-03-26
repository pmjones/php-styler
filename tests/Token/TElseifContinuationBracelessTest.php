<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TElseifContinuationBracelessTest extends TTestCase
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
                if (true)
                    $foo = 1;
                elseif (false)
                    $bar = 2;
                elseif (true)
                    $baz = 3;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TIf::class,
                    TIfOpeningParen::class,
                    TTrue::class,
                    TIfClosingParen::class,
                    TIfOpeningBrace::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                    TIfContinuationBrace::class,
                    TElseif::class,
                    TElseifOpeningParen::class,
                    TFalse::class,
                    TElseifClosingParen::class,
                    TElseifOpeningBrace::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                    TElseifContinuationBrace::class,
                    TElseif::class,
                    TElseifOpeningParen::class,
                    TTrue::class,
                    TElseifClosingParen::class,
                    TElseifOpeningBrace::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                    TElseifClosingBrace::class,
                ],
            ],
        ];
    }
}
