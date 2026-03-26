<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TAttributeCommaTest extends TTestCase
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
                #[Foo, Bar]
                function baz() {}
                CODE,
                [
                    TPhpOpeningTag::class,
                    TAttribute::class,
                    TUnqualifiedName::class,
                    TAttributeClosingBracket::class,
                    TAttribute::class,
                    TUnknownString::class,
                    TAttributeClosingBracket::class,
                    TFunction::class,
                    TFunctionName::class,
                    TParamsOpeningParen::class,
                    TParamsClosingParen::class,
                    TFunctionOpeningBrace::class,
                    TFunctionClosingBrace::class,
                ],
            ],
        ];
    }
}
