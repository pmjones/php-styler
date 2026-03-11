<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TObjectCastTest extends TTestCase
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
                $foo = (object) $bar;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TObjectCast::class,
                    TVariable::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
