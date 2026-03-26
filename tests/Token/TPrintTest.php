<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TPrintTest extends TTestCase
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
                print $foo;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TPrint::class,
                    TVariable::class,
                    TSemicolon::class,
                ],
            ],
            'parens' => [
                <<<'CODE'
                <?php
                print ($foo);
                CODE,
                [
                    TPhpOpeningTag::class,
                    TPrint::class,
                    TVariable::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
