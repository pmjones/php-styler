<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TNullsafeObjectOperatorTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'basic' => [
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
