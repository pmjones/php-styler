<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TConstTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'const' => [
                <<<'CODE'
                <?php
                const FOO = 1;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TConst::class,
                    TConstantName::class,
                    TAssignConst::class,
                    TIntegerLiteral::class,
                    TNamespaceConstEndSemicolon::class,
                ],
            ],
            'class-const' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    const BAR = 1;
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TClass::class,
                    TClassName::class,
                    TClassOpeningBrace::class,
                    TConst::class,
                    TConstantName::class,
                    TAssignConst::class,
                    TIntegerLiteral::class,
                    TConstEndSemicolon::class,
                    TClassClosingBrace::class,
                ],
            ],
            'multi-const' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    const A = 1, B = 2;
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TClass::class,
                    TClassName::class,
                    TClassOpeningBrace::class,
                    TConst::class,
                    TConstantName::class,
                    TAssignConst::class,
                    TIntegerLiteral::class,
                    TConstComma::class,
                    TConstantName::class,
                    TAssignConst::class,
                    TIntegerLiteral::class,
                    TConstEndSemicolon::class,
                    TClassClosingBrace::class,
                ],
            ],
            'namespace-multi-const' => [
                <<<'CODE'
                <?php
                const A = 1, B = 2;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TConst::class,
                    TConstantName::class,
                    TAssignConst::class,
                    TIntegerLiteral::class,
                    TConstComma::class,
                    TConstantName::class,
                    TAssignConst::class,
                    TIntegerLiteral::class,
                    TNamespaceConstEndSemicolon::class,
                ],
            ],
            'visibility-const' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public const A = 1;
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TClass::class,
                    TClassName::class,
                    TClassOpeningBrace::class,
                    TPublic::class,
                    TConst::class,
                    TConstantName::class,
                    TAssignConst::class,
                    TIntegerLiteral::class,
                    TConstEndSemicolon::class,
                    TClassClosingBrace::class,
                ],
            ],
            'typed-const' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    const int A = 1;
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TClass::class,
                    TClassName::class,
                    TClassOpeningBrace::class,
                    TConst::class,
                    TInt::class,
                    TUnknownString::class,
                    TAssignConst::class,
                    TIntegerLiteral::class,
                    TConstEndSemicolon::class,
                    TClassClosingBrace::class,
                ],
            ],
            'visibility-typed-const' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public const int A = 1;
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TClass::class,
                    TClassName::class,
                    TClassOpeningBrace::class,
                    TPublic::class,
                    TConst::class,
                    TInt::class,
                    TUnknownString::class,
                    TAssignConst::class,
                    TIntegerLiteral::class,
                    TConstEndSemicolon::class,
                    TClassClosingBrace::class,
                ],
            ],
            'use-const-import' => [
                <<<'CODE'
                <?php
                use const Foo\BAR;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TUse::class,
                    TUseConst::class,
                    TQualifiedName::class,
                    TUseEndSemicolon::class,
                ],
            ],
        ];
    }
}
