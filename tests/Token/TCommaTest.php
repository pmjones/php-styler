<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TCommaTest extends TTestCase
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
                foo($bar, $baz);
                CODE,
                [
                    TPhpOpeningTag::class,
                    TFunctionCallName::class,
                    TArgsOpeningParen::class,
                    TVariable::class,
                    TArgsComma::class,
                    TVariable::class,
                    TArgsClosingParen::class,
                    TSemicolon::class,
                ],
            ],
            'unclassified-nesting' => [
                <<<'CODE'
                <?php
                if ($x) {
                }

                [$a, $b] = $c;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TIf::class,
                    TIfOpeningParen::class,
                    TVariable::class,
                    TIfClosingParen::class,
                    TIfOpeningBrace::class,
                    TIfClosingBrace::class,
                    TArrayElementOpeningBracket::class,
                    TVariable::class,
                    TComma::class,
                    TVariable::class,
                    TArrayElementClosingBracket::class,
                    TAssign::class,
                    TVariable::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
