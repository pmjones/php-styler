<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TUnsetCastTest extends TTestCase
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
                $foo = (unset) $bar;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TUnsetCast::class,
                    TVariable::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
