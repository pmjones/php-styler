<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TUseVariablesOpeningParenTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'basic' => [
                <<<'CODE'
                <?php
                $foo = function () use ($bar) {
                };
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TAnonymousFunction::class,
                    TParamsOpeningParen::class,
                    TParamsClosingParen::class,
                    TUse::class,
                    TUseVariablesOpeningParen::class,
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
