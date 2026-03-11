<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TContinuationBraceTest extends TTestCase
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
                if ($foo) {
                    $bar = 1;
                } else {
                    $bar = 2;
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TIf::class,
                    TIfOpeningParen::class,
                    TVariable::class,
                    TIfClosingParen::class,
                    TIfOpeningBrace::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                    TIfContinuationBrace::class,
                    TElse::class,
                    TElseOpeningBrace::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                    TElseClosingBrace::class,
                ],
            ],
        ];
    }
}
