<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TInstanceofTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'instanceof' => [
                <<<'CODE'
                <?php
                $foo = $bar instanceof Baz;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TVariable::class,
                    TInstanceof::class,
                    TUnqualifiedName::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
