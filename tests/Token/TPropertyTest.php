<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TPropertyTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'untyped' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public $a;
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
                    TClassClosingBrace::class,
                ],
            ],
            'typed' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public int $a;
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
                    TClassClosingBrace::class,
                ],
            ],
            'with-static' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public static int $a;
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
                    TClassClosingBrace::class,
                ],
            ],
            'with-readonly' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    protected readonly int $a;
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
                    TClassClosingBrace::class,
                ],
            ],
            'nullable' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public ?int $a;
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
                    TClassClosingBrace::class,
                ],
            ],
            'union-type' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public int|string $a;
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
                    TClassClosingBrace::class,
                ],
            ],
            'intersection-type' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public Foo&Bar $a;
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
                    TClassClosingBrace::class,
                ],
            ],
            'class-type' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public Bar $a;
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
                    TClassClosingBrace::class,
                ],
            ],
            'qualified-class-type' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public Bar\Baz $a;
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
                    TClassClosingBrace::class,
                ],
            ],
            'fully-qualified-class-type' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public \Bar\Baz $a;
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TClass::class,
                    TClassName::class,
                    TClassOpeningBrace::class,
                    TPublic::class,
                    TFullyQualifiedName::class,
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
                    var $a;
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
                    TClassClosingBrace::class,
                ],
            ],
            ...(
                PHP_VERSION_ID
                    >= 80400
                ? [
                    'private-set' => [
                        <<<'CODE'
                    <?php
                    class Foo
                    {
                        public private(set) string $a;
                    }
                    CODE,
                        [
                            TPhpOpeningTag::class,
                            TClass::class,
                            TClassName::class,
                            TClassOpeningBrace::class,
                            TPublic::class,
                            TPrivateSet::class,
                            TString::class,
                            TVariable::class,
                            TPropertyEndSemicolon::class,
                            TClassClosingBrace::class,
                        ],
                    ],
                ]
                : [
                ]
            ),
            'with-default' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public int $a = 1;
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
                    TClassClosingBrace::class,
                ],
            ],
        ];
    }
}
