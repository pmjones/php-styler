<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TDynamicVariableOpeningBraceTest extends TTestCase
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
                ${$foo};
                CODE,
                [
                    TPhpOpeningTag::class,
                    TDollar::class,
                    TDynamicVariableOpeningBrace::class,
                    TVariable::class,
                    TDynamicVariableClosingBrace::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
