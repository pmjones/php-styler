<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TKeywordAsNameTest extends TTestCase
{
    private const KEYWORDS = [
        'abstract',
        'array',
        'as',
        'break',
        'callable',
        'case',
        'catch',
        'class',
        'clone',
        'const',
        'continue',
        'declare',
        'default',
        'do',
        'echo',
        'else',
        'elseif',
        'empty',
        'enddeclare',
        'endfor',
        'endforeach',
        'endif',
        'endswitch',
        'endwhile',
        'enum',
        'eval',
        'exit',
        'extends',
        'final',
        'finally',
        'fn',
        'for',
        'foreach',
        'global',
        'goto',
        'if',
        'implements',
        'include',
        'include_once',
        'instanceof',
        'insteadof',
        'interface',
        'isset',
        'list',
        'match',
        'namespace',
        'new',
        'print',
        'private',
        'protected',
        'public',
        'readonly',
        'require',
        'require_once',
        'return',
        'static',
        'switch',
        'throw',
        'trait',
        'try',
        'unset',
        'use',
        'var',
        'while',
        'yield',
    ];

    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        $staticMethodCallExpect = [
            TPhpOpeningTagInline::class,
            TUnqualifiedName::class,
            TMemberDoubleColon::class,
            TStaticMethodCallName::class,
            TArgsOpeningParen::class,
            TArgsClosingParen::class,
            TSemicolon::class,
        ];

        $staticMemberExpect = [
            TPhpOpeningTagInline::class,
            TUnqualifiedName::class,
            TMemberDoubleColon::class,
            TStaticMemberName::class,
            TSemicolon::class,
        ];

        $methodCallExpect = [
            TPhpOpeningTagInline::class,
            TVariable::class,
            TObjectOperator::class,
            TMethodCallName::class,
            TArgsOpeningParen::class,
            TArgsClosingParen::class,
            TSemicolon::class,
        ];

        $propertyAccessExpect = [
            TPhpOpeningTagInline::class,
            TVariable::class,
            TObjectOperator::class,
            TPropertyAccessName::class,
            TSemicolon::class,
        ];

        $nullsafeMethodCallExpect = [
            TPhpOpeningTagInline::class,
            TVariable::class,
            TNullsafeObjectOperator::class,
            TMethodCallName::class,
            TArgsOpeningParen::class,
            TArgsClosingParen::class,
            TSemicolon::class,
        ];

        $nullsafePropertyAccessExpect = [
            TPhpOpeningTagInline::class,
            TVariable::class,
            TNullsafeObjectOperator::class,
            TPropertyAccessName::class,
            TSemicolon::class,
        ];

        $methodNameExpect = [
            TPhpOpeningTagInline::class,
            TClass::class,
            TClassName::class,
            TClassOpeningBrace::class,
            TFunction::class,
            TFunctionName::class,
            TParamsOpeningParen::class,
            TParamsClosingParen::class,
            TFunctionOpeningBrace::class,
            TFunctionClosingBrace::class,
            TClassClosingBrace::class,
        ];

        $methodNameWithReferenceExpect = [
            TPhpOpeningTagInline::class,
            TClass::class,
            TClassName::class,
            TClassOpeningBrace::class,
            TFunction::class,
            TReference::class,
            TFunctionName::class,
            TParamsOpeningParen::class,
            TParamsClosingParen::class,
            TFunctionOpeningBrace::class,
            TFunctionClosingBrace::class,
            TClassClosingBrace::class,
        ];

        /** @php-styler-expansive */
        $cases = [
            // method name with return type
            'method-name-use-with-return-type' => [
                '<?php class Foo { function use() : string {} }',
                [
                    TPhpOpeningTagInline::class,
                    TClass::class,
                    TClassName::class,
                    TClassOpeningBrace::class,
                    TFunction::class,
                    TFunctionName::class,
                    TParamsOpeningParen::class,
                    TParamsClosingParen::class,
                    TReturnColon::class,
                    TString::class,
                    TFunctionOpeningBrace::class,
                    TFunctionClosingBrace::class,
                    TClassClosingBrace::class,
                ],
            ],
            // method name with return type and reference
            'method-name-use-with-reference-and-return-type' => [
                '<?php class Foo { function &use() : string {} }',
                [
                    TPhpOpeningTagInline::class,
                    TClass::class,
                    TClassName::class,
                    TClassOpeningBrace::class,
                    TFunction::class,
                    TReference::class,
                    TFunctionName::class,
                    TParamsOpeningParen::class,
                    TParamsClosingParen::class,
                    TReturnColon::class,
                    TString::class,
                    TFunctionOpeningBrace::class,
                    TFunctionClosingBrace::class,
                    TClassClosingBrace::class,
                ],
            ],
        ];

        foreach (self::KEYWORDS as $keyword) {
            // Foo::keyword()
            $cases["static-method-call-{$keyword}"] = [
                "<?php Foo::{$keyword}();",
                $staticMethodCallExpect,
            ];

            // Foo::keyword
            $cases["static-member-{$keyword}"] = [
                "<?php Foo::{$keyword};",
                $staticMemberExpect,
            ];

            // $obj->keyword()
            $cases["method-call-{$keyword}"] = [
                "<?php \$obj->{$keyword}();",
                $methodCallExpect,
            ];

            // $obj->keyword
            $cases["property-access-{$keyword}"] = [
                "<?php \$obj->{$keyword};",
                $propertyAccessExpect,
            ];

            // $obj?->keyword()
            $cases["nullsafe-method-call-{$keyword}"] = [
                "<?php \$obj?->{$keyword}();",
                $nullsafeMethodCallExpect,
            ];

            // $obj?->keyword
            $cases["nullsafe-property-access-{$keyword}"] = [
                "<?php \$obj?->{$keyword};",
                $nullsafePropertyAccessExpect,
            ];

            // class Foo { function keyword() {} }
            $cases["method-name-{$keyword}"] = [
                "<?php class Foo { function {$keyword}() {} }",
                $methodNameExpect,
            ];

            // class Foo { function &keyword() {} }
            $cases["method-name-with-reference-{$keyword}"] = [
                "<?php class Foo { function &{$keyword}() {} }",
                $methodNameWithReferenceExpect,
            ];
        }

        return $cases;
    }
}
