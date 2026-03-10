<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TDoubleCastTest extends TTestCase
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
                $foo = (double) $bar;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TDoubleCast::class,
                    TVariable::class,
                    TSemicolon::class,
                ],
            ],
            'float' => [
                <<<'CODE'
                <?php
                $foo = (float) $bar;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TDoubleCast::class,
                    TVariable::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
