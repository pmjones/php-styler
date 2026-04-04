<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TUseFunctionOpeningBraceTest extends TTestCase
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
                use Foo\{function bar, function baz};
                CODE,
                [
                    TPhpOpeningTag::class,
                    TUse::class,
                    TUnqualifiedName::class,
                    TNamespaceSeparator::class,
                    TUseOpeningBrace::class,
                    TUseFunction::class,
                    TFunctionName::class,
                    TUseComma::class,
                    TFunction::class,
                    TFunctionName::class,
                    TFunctionClosingBrace::class,
                    TUseEndSemicolon::class,
                ],
            ],
        ];
    }
}
