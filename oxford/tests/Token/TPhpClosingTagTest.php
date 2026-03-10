<?php
declare(strict_types=1);

namespace Oxford\Token;

class TPhpClosingTagTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
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
