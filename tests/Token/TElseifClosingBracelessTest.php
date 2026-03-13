<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TElseifClosingBracelessTest extends TTestCase
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
                CODE,
                [
                    TPhpOpeningTag::class,
                    TIf::class,
                    TIfOpeningParen::class,
                    TTrue::class,
                    TIfClosingParen::class,
                    TOpeningBraceless::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                    TIfContinuationBraceless::class,
                    TElseif::class,
                    TElseifOpeningParen::class,
                    TFalse::class,
                    TElseifClosingParen::class,
                    TOpeningBraceless::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                    TElseifClosingBraceless::class,
                ],
            ],
        ];
    }
}
