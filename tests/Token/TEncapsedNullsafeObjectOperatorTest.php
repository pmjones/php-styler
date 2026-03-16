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
                    TDoubleQuoteOpening::class,
                    TCurlyOpen::class,
                    TEncapsedVariable::class,
                    TEncapsedNullsafeObjectOperator::class,
                    TEncapsedPropertyAccessName::class,
                    TCurlyClose::class,
                    TDoubleQuoteClosing::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
