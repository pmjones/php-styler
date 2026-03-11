<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TIntCastTest extends TTestCase
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
                $foo = (int) $bar;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TIntCast::class,
                    TVariable::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
