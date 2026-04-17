<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TInlineHtmlTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'inline-html-before-php' => [
                "hello <?php echo 1;",
                [
                    TInlineHtml::class,
                    TPhpOpeningTagContinuation::class,
                    TEcho::class,
                    TIntegerLiteral::class,
                    TEchoEndSemicolon::class,
                ],
            ],
            'inline-html-after-php' => [
                "<?php echo 1; ?> hello",
                [
                    TPhpOpeningTagInline::class,
                    TEcho::class,
                    TIntegerLiteral::class,
                    TEchoEndSemicolon::class,
                    TPhpClosingTagContinuation::class,
                    TInlineHtml::class,
                ],
            ],
            'inline-html-between-php' => [
                "<?php echo 1; ?> hello <?php echo 2;",
                [
                    TPhpOpeningTagInline::class,
                    TEcho::class,
                    TIntegerLiteral::class,
                    TEchoEndSemicolon::class,
                    TPhpClosingTagContinuation::class,
                    TInlineHtml::class,
                    TPhpOpeningTagContinuation::class,
                    TEcho::class,
                    TIntegerLiteral::class,
                    TEchoEndSemicolon::class,
                ],
            ],
        ];
    }
}
