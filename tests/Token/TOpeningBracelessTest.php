<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TOpeningBracelessTest extends TTestCase
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
                if ($foo) $bar = 1;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TIf::class,
                    TIfOpeningParen::class,
                    TVariable::class,
                    TIfClosingParen::class,
                    TOpeningBraceless::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                    TIfClosingBraceless::class,
                ],
            ],
        ];
    }
}
