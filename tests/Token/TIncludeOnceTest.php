<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TIncludeOnceTest extends TTestCase
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
                include_once $file;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TIncludeOnce::class,
                    TVariable::class,
                    TSemicolon::class,
                ],
            ],
            'parens' => [
                <<<'CODE'
                <?php
                include_once ($file);
                CODE,
                [
                    TPhpOpeningTag::class,
                    TIncludeOnce::class,
                    TVariable::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
