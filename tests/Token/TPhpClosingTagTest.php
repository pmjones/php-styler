<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TPhpClosingTagTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'close-tag' => [
                <<<'CODE'
                <?php echo 1; ?>
                CODE,
                [
                    TPhpOpeningTagInline::class,
                    TEcho::class,
                    TIntegerLiteral::class,
                    TEchoEndSemicolon::class,
                    TPhpClosingTag::class,
                ],
            ],
        ];
    }
}
