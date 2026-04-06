<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Line;
use PHPUnit\Framework\Attributes\DataProvider;

class TCommentStarredTest extends TTestCase
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
                $foo = 1;
                /*
                 * bar
                 */
                $bar = 1;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                    TCommentStarred::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                ],
            ],
            'open-tag' => [
                <<<'CODE'
                <?php
                /*
                 * foo
                 */
                CODE,
                [
                    TPhpOpeningTag::class,
                    TCommentStarredMidStatement::class,
                ],
            ],
            'oneline-basic' => [
                <<<'CODE'
                <?php
                $foo = 1;
                /* bar */
                $bar = 1;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                    TCommentStarred::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                ],
            ],
            'oneline-open-tag' => [
                <<<'CODE'
                <?php
                /* foo */
                $foo = 1;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TCommentStarred::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }

    /** @return array<string, array{0: string, 1: int, 2: string}> */
    public static function provideRender() : array
    {
        /** @php-styler-expansive */
        return [
            'reindent-to-1' => [
                "/*\n * Foo.\n * More text.\n */",
                1,
                "/*\n     * Foo.\n     * More text.\n     */",
            ],
            'reindent-to-2' => [
                "/*\n * Foo.\n */",
                2,
                "/*\n         * Foo.\n         */",
            ],
            'reindent-to-0' => [
                "/*\n     * Foo.\n     */",
                0,
                "/*\n * Foo.\n */",
            ],
        ];
    }

    #[DataProvider('provideRender')]
    public function testRender(string $text, int $indent, string $expect) : void
    {
        $token = new TCommentStarred(T_COMMENT, $text);
        $line = new Line(indent: $indent);
        $this->assertSame($expect, $token->render($line));
    }
}
