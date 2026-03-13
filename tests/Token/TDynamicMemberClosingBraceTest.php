<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TDynamicMemberClosingBraceTest extends TTestCase
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
                $foo->{$bar};
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TObjectOperator::class,
                    TDynamicMemberOpeningBrace::class,
                    TVariable::class,
                    TDynamicMemberClosingBrace::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
