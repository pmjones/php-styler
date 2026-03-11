<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TStringTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'true' => [
                <<<'CODE'
                <?php
                true;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TTrue::class,
                    TSemicolon::class,
                ],
            ],
            'false' => [
                <<<'CODE'
                <?php
                false;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TFalse::class,
                    TSemicolon::class,
                ],
            ],
            'null' => [
                <<<'CODE'
                <?php
                null;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TNull::class,
                    TSemicolon::class,
                ],
            ],
            'parent' => [
                <<<'CODE'
                <?php
                parent;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TParent::class,
                    TSemicolon::class,
                ],
            ],
            'string' => [
                <<<'CODE'
                <?php
                function foo(string $bar) {}
                CODE,
                [
                    TPhpOpeningTag::class,
                    TFunction::class,
                    TFunctionName::class,
                    TParamsOpeningParen::class,
                    TString::class,
                    TVariable::class,
                    TParamsClosingParen::class,
                    TFunctionOpeningBrace::class,
                    TFunctionClosingBrace::class,
                ],
            ],
        ];
    }
}
