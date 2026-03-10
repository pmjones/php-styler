<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TAnonymousFunctionTest extends TTestCase
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
                function () {
                };
                CODE,
                [
                    TPhpOpeningTag::class,
                    TAnonymousFunction::class,
                    TParamsOpeningParen::class,
                    TParamsClosingParen::class,
                    TAnonymousOpeningBrace::class,
                    TAnonymousClosingBrace::class,
                    TSemicolon::class,
                ],
            ],
            'return-string' => [
                <<<'CODE'
                <?php
                function () : Foo {
                };
                CODE,
                [
                    TPhpOpeningTag::class,
                    TAnonymousFunction::class,
                    TParamsOpeningParen::class,
                    TParamsClosingParen::class,
                    TReturnColon::class,
                    TUnqualifiedName::class,
                    TAnonymousOpeningBrace::class,
                    TAnonymousClosingBrace::class,
                    TSemicolon::class,
                ],
            ],
            'return-name-qualified' => [
                <<<'CODE'
                <?php
                function () : Foo\Bar {
                };
                CODE,
                [
                    TPhpOpeningTag::class,
                    TAnonymousFunction::class,
                    TParamsOpeningParen::class,
                    TParamsClosingParen::class,
                    TReturnColon::class,
                    TQualifiedName::class,
                    TAnonymousOpeningBrace::class,
                    TAnonymousClosingBrace::class,
                    TSemicolon::class,
                ],
            ],
            'return-name-fully-qualified' => [
                <<<'CODE'
                <?php
                function () : \Foo\Bar {
                };
                CODE,
                [
                    TPhpOpeningTag::class,
                    TAnonymousFunction::class,
                    TParamsOpeningParen::class,
                    TParamsClosingParen::class,
                    TReturnColon::class,
                    TFullyQualifiedName::class,
                    TAnonymousOpeningBrace::class,
                    TAnonymousClosingBrace::class,
                    TSemicolon::class,
                ],
            ],
            'use' => [
                <<<'CODE'
                <?php
                function () use ($foo) {
                };
                CODE,
                [
                    TPhpOpeningTag::class,
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
            'use-return-string' => [
                <<<'CODE'
                <?php
                function () use ($foo) : Foo {
                };
                CODE,
                [
                    TPhpOpeningTag::class,
                    TAnonymousFunction::class,
                    TParamsOpeningParen::class,
                    TParamsClosingParen::class,
                    TUse::class,
                    TUseVariablesOpeningParen::class,
                    TVariable::class,
                    TUseVariablesClosingParen::class,
                    TReturnColon::class,
                    TUnqualifiedName::class,
                    TAnonymousOpeningBrace::class,
                    TAnonymousClosingBrace::class,
                    TSemicolon::class,
                ],
            ],
            'use-return-name-qualified' => [
                <<<'CODE'
                <?php
                function () use ($foo) : Foo\Bar {
                };
                CODE,
                [
                    TPhpOpeningTag::class,
                    TAnonymousFunction::class,
                    TParamsOpeningParen::class,
                    TParamsClosingParen::class,
                    TUse::class,
                    TUseVariablesOpeningParen::class,
                    TVariable::class,
                    TUseVariablesClosingParen::class,
                    TReturnColon::class,
                    TQualifiedName::class,
                    TAnonymousOpeningBrace::class,
                    TAnonymousClosingBrace::class,
                    TSemicolon::class,
                ],
            ],
            'use-return-name-fully-qualified' => [
                <<<'CODE'
                <?php
                function () use ($foo) : \Foo\Bar {
                };
                CODE,
                [
                    TPhpOpeningTag::class,
                    TAnonymousFunction::class,
                    TParamsOpeningParen::class,
                    TParamsClosingParen::class,
                    TUse::class,
                    TUseVariablesOpeningParen::class,
                    TVariable::class,
                    TUseVariablesClosingParen::class,
                    TReturnColon::class,
                    TFullyQualifiedName::class,
                    TAnonymousOpeningBrace::class,
                    TAnonymousClosingBrace::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
