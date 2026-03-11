<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TListTest extends TTestCase
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
                list($foo, $bar) = $baz;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TList::class,
                    TArgsOpeningParen::class,
                    TVariable::class,
                    TArgsComma::class,
                    TVariable::class,
                    TArgsClosingParen::class,
                    TAssign::class,
                    TVariable::class,
                    TSemicolon::class,
                ],
            ],
            'with-keys' => [
                <<<'CODE'
                <?php
                list(0 => $foo, 1 => $bar) = $baz;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TList::class,
                    TArgsOpeningParen::class,
                    TIntegerLiteral::class,
                    TDoubleArrow::class,
                    TVariable::class,
                    TArgsComma::class,
                    TIntegerLiteral::class,
                    TDoubleArrow::class,
                    TVariable::class,
                    TArgsClosingParen::class,
                    TAssign::class,
                    TVariable::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
