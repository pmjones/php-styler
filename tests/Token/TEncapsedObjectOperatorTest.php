<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TEncapsedObjectOperatorTest extends TTestCase
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
                $foo = "{$bar->baz}";
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TDoubleQuote::class,
                    TCurlyOpen::class,
                    TEncapsedVariable::class,
                    TEncapsedObjectOperator::class,
                    TEncapsedPropertyAccessName::class,
                    TCurlyClose::class,
                    TDoubleQuote::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
