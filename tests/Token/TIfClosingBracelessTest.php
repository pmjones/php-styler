<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TIfClosingBracelessTest extends TTestCase
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
                    TIfClosingBrace::class,
                ],
            ],
        ];
    }
}
