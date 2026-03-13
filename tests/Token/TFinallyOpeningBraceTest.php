<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TFinallyOpeningBraceTest extends TTestCase
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
                try {
                } finally {
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TTry::class,
                    TTryOpeningBrace::class,
                    TTryContinuationBrace::class,
                    TFinally::class,
                    TFinallyOpeningBrace::class,
                    TFinallyClosingBrace::class,
                ],
            ],
        ];
    }
}
