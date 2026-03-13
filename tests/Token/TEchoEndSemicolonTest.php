<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TEchoEndSemicolonTest extends TTestCase
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
                echo "foo";
                CODE,
                [
                    TPhpOpeningTag::class,
                    TEcho::class,
                    TStringLiteral::class,
                    TEchoEndSemicolon::class,
                ],
            ],
        ];
    }
}
