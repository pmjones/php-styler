<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TElvisColonTest extends TTestCase
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
                $foo = $bar ?: "baz";
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TVariable::class,
                    TElvisQuestion::class,
                    TElvisColon::class,
                    TStringLiteral::class,
                    TElvisEndSemicolon::class,
                ],
            ],
        ];
    }
}
