<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TDotTest extends TTestCase
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
                $foo = 'bar' . 'baz';
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TStringLiteral::class,
                    TDot::class,
                    TStringLiteral::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
