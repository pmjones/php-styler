<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TIfTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'braced' => [
                <<<'CODE'
                <?php
                if ($foo == 88) {
                    $bar = 88;
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TIf::class,
                    TIfOpeningParen::class,
                    TVariable::class,
                    TIsEqual::class,
                    TIntegerLiteral::class,
                    TIfClosingParen::class,
                    TIfOpeningBrace::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                    TIfClosingBrace::class,
                ],
            ],
            'alternative' => [
                <<<'CODE'
                <?php
                if ($foo == 88):
                    $bar = 88;
                endif;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TIf::class,
                    TIfOpeningParen::class,
                    TVariable::class,
                    TIsEqual::class,
                    TIntegerLiteral::class,
                    TIfClosingParen::class,
                    TIfColon::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                    TEndif::class,
                    TSemicolon::class,
                ],
            ],
            'unbraced' => [
                <<<'CODE'
                <?php
                if ($foo == 88) $bar = 88;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TIf::class,
                    TIfOpeningParen::class,
                    TVariable::class,
                    TIsEqual::class,
                    TIntegerLiteral::class,
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
