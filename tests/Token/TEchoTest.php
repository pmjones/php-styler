<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TEchoTest extends TTestCase
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
                echo $foo;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TEcho::class,
                    TVariable::class,
                    TEchoEndSemicolon::class,
                ],
            ],
            'parens' => [
                <<<'CODE'
                <?php
                echo ($foo);
                CODE,
                [
                    TPhpOpeningTag::class,
                    TEcho::class,
                    TVariable::class,
                    TEchoEndSemicolon::class,
                ],
            ],
        ];
    }
}
