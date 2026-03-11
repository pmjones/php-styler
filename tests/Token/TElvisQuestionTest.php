<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TElvisQuestionTest extends TTestCase
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
                $foo = $bar ?: $baz;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TVariable::class,
                    TElvisQuestion::class,
                    TElvisColon::class,
                    TVariable::class,
                    TElvisEndSemicolon::class,
                ],
            ],
        ];
    }
}
