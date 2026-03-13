<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TEncapsedNullsafeObjectOperatorTest extends TTestCase
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
                $foo = "{$bar?->baz}";
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TDoubleQuote::class,
                    TCurlyOpen::class,
                    TVariable::class,
                    TEncapsedNullsafeObjectOperator::class,
                    TPropertyAccessName::class,
                    TCurlyClose::class,
                    TDoubleQuote::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
