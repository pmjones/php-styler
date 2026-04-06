<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TKeywordAsNameTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'keyword-constant-after-double-colon' => [
                '<?php Foo::CONST;',
                [
                    TPhpOpeningTagInline::class,
                    TUnqualifiedName::class,
                    TMemberDoubleColon::class,
                    TStaticMemberName::class,
                    TSemicolon::class,
                ],
            ],
            'keyword-constant-match-after-double-colon' => [
                '<?php Foo::MATCH;',
                [
                    TPhpOpeningTagInline::class,
                    TUnqualifiedName::class,
                    TMemberDoubleColon::class,
                    TStaticMemberName::class,
                    TSemicolon::class,
                ],
            ],
            'keyword-method-after-double-colon' => [
                '<?php Foo::match();',
                [
                    TPhpOpeningTagInline::class,
                    TUnqualifiedName::class,
                    TMemberDoubleColon::class,
                    TStaticMethodCallName::class,
                    TArgsOpeningParen::class,
                    TArgsClosingParen::class,
                    TSemicolon::class,
                ],
            ],
            'keyword-fn-method-after-double-colon' => [
                '<?php Foo::fn();',
                [
                    TPhpOpeningTagInline::class,
                    TUnqualifiedName::class,
                    TMemberDoubleColon::class,
                    TStaticMethodCallName::class,
                    TArgsOpeningParen::class,
                    TArgsClosingParen::class,
                    TSemicolon::class,
                ],
            ],
            'keyword-method-after-arrow' => [
                '<?php $obj->match();',
                [
                    TPhpOpeningTagInline::class,
                    TVariable::class,
                    TObjectOperator::class,
                    TMethodCallName::class,
                    TArgsOpeningParen::class,
                    TArgsClosingParen::class,
                    TSemicolon::class,
                ],
            ],
            'keyword-fn-method-after-arrow' => [
                '<?php $obj->fn();',
                [
                    TPhpOpeningTagInline::class,
                    TVariable::class,
                    TObjectOperator::class,
                    TMethodCallName::class,
                    TArgsOpeningParen::class,
                    TArgsClosingParen::class,
                    TSemicolon::class,
                ],
            ],
            'keyword-method-after-nullsafe' => [
                '<?php $obj?->match();',
                [
                    TPhpOpeningTagInline::class,
                    TVariable::class,
                    TNullsafeObjectOperator::class,
                    TMethodCallName::class,
                    TArgsOpeningParen::class,
                    TArgsClosingParen::class,
                    TSemicolon::class,
                ],
            ],
            'keyword-property-after-arrow' => [
                '<?php $obj->static;',
                [
                    TPhpOpeningTagInline::class,
                    TVariable::class,
                    TObjectOperator::class,
                    TPropertyAccessName::class,
                    TSemicolon::class,
                ],
            ],
            'keyword-const-property-after-arrow' => [
                '<?php $obj->const;',
                [
                    TPhpOpeningTagInline::class,
                    TVariable::class,
                    TObjectOperator::class,
                    TPropertyAccessName::class,
                    TSemicolon::class,
                ],
            ],
            'keyword-property-after-nullsafe' => [
                '<?php $obj?->static;',
                [
                    TPhpOpeningTagInline::class,
                    TVariable::class,
                    TNullsafeObjectOperator::class,
                    TPropertyAccessName::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
