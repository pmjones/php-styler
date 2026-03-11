<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TPropertyAccessNameTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'instance-property' => [
                <<<'CODE'
                <?php
                $foo->bar;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TObjectOperator::class,
                    TPropertyAccessName::class,
                    TSemicolon::class,
                ],
            ],
            'nullsafe-property' => [
                <<<'CODE'
                <?php
                $foo?->bar;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TNullsafeObjectOperator::class,
                    TPropertyAccessName::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
