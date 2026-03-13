<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TSwitchColonTest extends TTestCase
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
                switch ($foo):
                    case 1:
                        break;
                endswitch;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TSwitch::class,
                    TSwitchOpeningParen::class,
                    TVariable::class,
                    TSwitchClosingParen::class,
                    TSwitchColon::class,
                    TCase::class,
                    TIntegerLiteral::class,
                    TCaseColon::class,
                    TBreak::class,
                    TSemicolon::class,
                    TEndswitchAfterCase::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
