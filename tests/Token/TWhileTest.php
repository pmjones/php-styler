<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TWhileTest extends TTestCase
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
                while ($foo) {
                    $baz ++;
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TWhile::class,
                    TWhileOpeningParen::class,
                    TVariable::class,
                    TWhileClosingParen::class,
                    TWhileOpeningBrace::class,
                    TVariable::class,
                    TPostIncrement::class,
                    TSemicolon::class,
                    TWhileClosingBrace::class,
                ],
            ],
            'alternative' => [
                <<<'CODE'
                <?php
                while ($foo):
                    $baz ++;
                endwhile;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TWhile::class,
                    TWhileOpeningParen::class,
                    TVariable::class,
                    TWhileClosingParen::class,
                    TWhileColon::class,
                    TVariable::class,
                    TPostIncrement::class,
                    TSemicolon::class,
                    TEndwhile::class,
                    TSemicolon::class,
                ],
            ],
            'unbraced' => [
                <<<'CODE'
                <?php
                while ($foo) $baz ++;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TWhile::class,
                    TWhileOpeningParen::class,
                    TVariable::class,
                    TWhileClosingParen::class,
                    TWhileOpeningBrace::class,
                    TVariable::class,
                    TPostIncrement::class,
                    TSemicolon::class,
                    TWhileClosingBrace::class,
                ],
            ],
        ];
    }
}
