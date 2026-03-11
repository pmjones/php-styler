<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TMagicTraitConstantTest extends TTestCase
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
                $foo = __TRAIT__;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TMagicTraitConstant::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
