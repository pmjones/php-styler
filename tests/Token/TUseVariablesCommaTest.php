<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TUseVariablesCommaTest extends TTestCase
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
                $foo = function () use ($a, $b) {};
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TAnonymousFunction::class,
                    TParamsOpeningParen::class,
                    TParamsClosingParen::class,
                    TUseVariables::class,
                    TUseVariablesOpeningParen::class,
                    TVariable::class,
                    TUseVariablesComma::class,
                    TVariable::class,
                    TUseVariablesClosingParen::class,
                    TAnonymousOpeningBrace::class,
                    TAnonymousClosingBrace::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
