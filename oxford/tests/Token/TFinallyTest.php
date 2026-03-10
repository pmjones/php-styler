<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TFinallyTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'try-finally' => [
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
