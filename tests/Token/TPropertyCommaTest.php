<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TPropertyCommaTest extends TTestCase
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
                class Foo
                {
                    public int $a, $b;
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TClass::class,
                    TClassName::class,
                    TClassOpeningBrace::class,
                    TPublic::class,
                    TInt::class,
                    TVariable::class,
                    TPropertyEndSemicolon::class,
                    TPublic::class,
                    TInt::class,
                    TVariable::class,
                    TPropertyEndSemicolon::class,
                    TClassClosingBrace::class,
                ],
            ],
            'untyped' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public $a, $b;
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TClass::class,
                    TClassName::class,
                    TClassOpeningBrace::class,
                    TPublic::class,
                    TVariable::class,
                    TPropertyEndSemicolon::class,
                    TPublic::class,
                    TVariable::class,
                    TPropertyEndSemicolon::class,
                    TClassClosingBrace::class,
                ],
            ],
            'with-default-values' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public int $a = 1, $b = 2;
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TClass::class,
                    TClassName::class,
                    TClassOpeningBrace::class,
                    TPublic::class,
                    TInt::class,
                    TVariable::class,
                    TAssignProperty::class,
                    TIntegerLiteral::class,
                    TPropertyEndSemicolon::class,
                    TPublic::class,
                    TInt::class,
                    TVariable::class,
                    TAssignProperty::class,
                    TIntegerLiteral::class,
                    TPropertyEndSemicolon::class,
                    TClassClosingBrace::class,
                ],
            ],
            'nullable' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public ?int $a, $b;
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TClass::class,
                    TClassName::class,
                    TClassOpeningBrace::class,
                    TPublic::class,
                    TNullable::class,
                    TInt::class,
                    TVariable::class,
                    TPropertyEndSemicolon::class,
                    TPublic::class,
                    TNullable::class,
                    TInt::class,
                    TVariable::class,
                    TPropertyEndSemicolon::class,
                    TClassClosingBrace::class,
                ],
            ],
            'union-type' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public int|string $a, $b;
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TClass::class,
                    TClassName::class,
                    TClassOpeningBrace::class,
                    TPublic::class,
                    TInt::class,
                    TUnion::class,
                    TString::class,
                    TVariable::class,
                    TPropertyEndSemicolon::class,
                    TPublic::class,
                    TInt::class,
                    TUnion::class,
                    TString::class,
                    TVariable::class,
                    TPropertyEndSemicolon::class,
                    TClassClosingBrace::class,
                ],
            ],
            'intersection-type' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public Foo&Bar $a, $b;
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TClass::class,
                    TClassName::class,
                    TClassOpeningBrace::class,
                    TPublic::class,
                    TUnqualifiedName::class,
                    TIntersection::class,
                    TUnqualifiedName::class,
                    TVariable::class,
                    TPropertyEndSemicolon::class,
                    TPublic::class,
                    TUnqualifiedName::class,
                    TIntersection::class,
                    TUnqualifiedName::class,
                    TVariable::class,
                    TPropertyEndSemicolon::class,
                    TClassClosingBrace::class,
                ],
            ],
            'class-type' => [
                <<<'CODE'
                <?php
                class Baz
                {
                    public Bar $a, $b;
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TClass::class,
                    TClassName::class,
                    TClassOpeningBrace::class,
                    TPublic::class,
                    TUnqualifiedName::class,
                    TVariable::class,
                    TPropertyEndSemicolon::class,
                    TPublic::class,
                    TUnqualifiedName::class,
                    TVariable::class,
                    TPropertyEndSemicolon::class,
                    TClassClosingBrace::class,
                ],
            ],
            'qualified-class-type' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public Bar\Baz $a, $b;
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TClass::class,
                    TClassName::class,
                    TClassOpeningBrace::class,
                    TPublic::class,
                    TQualifiedName::class,
                    TVariable::class,
                    TPropertyEndSemicolon::class,
                    TPublic::class,
                    TQualifiedName::class,
                    TVariable::class,
                    TPropertyEndSemicolon::class,
                    TClassClosingBrace::class,
                ],
            ],
            'var' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    var $a, $b;
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TClass::class,
                    TClassName::class,
                    TClassOpeningBrace::class,
                    TVar::class,
                    TVariable::class,
                    TPropertyEndSemicolon::class,
                    TVar::class,
                    TVariable::class,
                    TPropertyEndSemicolon::class,
                    TClassClosingBrace::class,
                ],
            ],
            'with-static' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public static int $a, $b;
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TClass::class,
                    TClassName::class,
                    TClassOpeningBrace::class,
                    TPublic::class,
                    TStatic::class,
                    TInt::class,
                    TVariable::class,
                    TPropertyEndSemicolon::class,
                    TPublic::class,
                    TStatic::class,
                    TInt::class,
                    TVariable::class,
                    TPropertyEndSemicolon::class,
                    TClassClosingBrace::class,
                ],
            ],
            'with-readonly' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    protected readonly int $a, $b;
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TClass::class,
                    TClassName::class,
                    TClassOpeningBrace::class,
                    TProtected::class,
                    TReadonly::class,
                    TInt::class,
                    TVariable::class,
                    TPropertyEndSemicolon::class,
                    TProtected::class,
                    TReadonly::class,
                    TInt::class,
                    TVariable::class,
                    TPropertyEndSemicolon::class,
                    TClassClosingBrace::class,
                ],
            ],
            'three-properties' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public int $a, $b, $c;
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TClass::class,
                    TClassName::class,
                    TClassOpeningBrace::class,
                    TPublic::class,
                    TInt::class,
                    TVariable::class,
                    TPropertyEndSemicolon::class,
                    TPublic::class,
                    TInt::class,
                    TVariable::class,
                    TPropertyEndSemicolon::class,
                    TPublic::class,
                    TInt::class,
                    TVariable::class,
                    TPropertyEndSemicolon::class,
                    TClassClosingBrace::class,
                ],
            ],
            'with-array-values' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public $a = [1, 2], $b = 3;
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TClass::class,
                    TClassName::class,
                    TClassOpeningBrace::class,
                    TPublic::class,
                    TVariable::class,
                    TAssignProperty::class,
                    TArrayOpeningBracket::class,
                    TIntegerLiteral::class,
                    TArrayComma::class,
                    TIntegerLiteral::class,
                    TArrayClosingBracket::class,
                    TPropertyEndSemicolon::class,
                    TPublic::class,
                    TVariable::class,
                    TAssignProperty::class,
                    TIntegerLiteral::class,
                    TPropertyEndSemicolon::class,
                    TClassClosingBrace::class,
                ],
            ],
        ];
    }
}
