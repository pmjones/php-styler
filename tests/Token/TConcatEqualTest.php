<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TConcatEqualTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'concat-equal' => [
                <<<'CODE'
                <?php
                $foo .= 'bar';
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TConcatEqual::class,
                    TStringLiteral::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
