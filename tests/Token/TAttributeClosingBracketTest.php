<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TAttributeClosingBracketTest extends TTestCase
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
                #[Foo]
                function bar() {
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TAttribute::class,
                    TUnqualifiedName::class,
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
