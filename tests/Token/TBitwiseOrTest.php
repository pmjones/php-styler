<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TBitwiseOrTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'bitwise-or' => [
                <<<'CODE'
                <?php
                $foo = 1 | 2;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TBitwiseOr::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
