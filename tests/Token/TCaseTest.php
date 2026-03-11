<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TCaseTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'switch-case' => [
                <<<'CODE'
                <?php
                switch ($foo) {
                    case 'bar':
                        break;
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TSwitch::class,
                    TSwitchOpeningParen::class,
                    TVariable::class,
                    TSwitchClosingParen::class,
                    TSwitchOpeningBrace::class,
                    TCase::class,
                    TStringLiteral::class,
                    TCaseColon::class,
                    TBreak::class,
                    TSemicolon::class,
                    TSwitchAfterCaseClosingBrace::class,
                ],
            ],
        ];
    }
}
