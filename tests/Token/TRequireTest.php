<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TRequireTest extends TTestCase
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
                require $file;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TRequire::class,
                    TVariable::class,
                    TSemicolon::class,
                ],
            ],
            'parens' => [
                <<<'CODE'
                <?php
                require ($file);
                CODE,
                [
                    TPhpOpeningTag::class,
                    TRequire::class,
                    TVariable::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
