<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TPhpEchoOpeningTagTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'basic' => [
                '<?= $foo ?>',
                [
                    TPhpEchoOpeningTag::class,
                    TVariable::class,
                    TPhpClosingTag::class,
                ],
            ],
            'echo-with-trailing-content' => [
                '<?= $foo ?> trailing',
                [
                    TPhpEchoOpeningTag::class,
                    TVariable::class,
                    TPhpClosingTagContinuation::class,
                    TInlineHtml::class,
                ],
            ],
            'echo-stmt-with-closing-tag' => [
                '<?php echo $foo ?> trailing',
                [
                    TPhpOpeningTagInline::class,
                    TEcho::class,
                    TVariable::class,
                    TEchoEndSemicolon::class,
                    TPhpClosingTagContinuation::class,
                    TInlineHtml::class,
                ],
            ],
        ];
    }
}
