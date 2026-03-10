<?php
declare(strict_types=1);

namespace Oxford\Token;

use Oxford\Line;

class TDocCommentTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'basic' => [
                <<<'CODE'
                <?php
                /**
                 * Foo
                 */
                $foo = 1;

                /**
                 * Bar
                 */
                $bar = 2;

                CODE,
                [
                    TPhpOpeningTag::class,
                    TDocComment::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                    TDocComment::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideRender
     */
    public function testRender(string $text, int $indent, string $expect) : void
    {
        $token = new TDocComment(T_DOC_COMMENT, $text);
        $line = new Line(indent: $indent);
        $this->assertSame($expect, $token->render($line));
    }

    /** @return array<string, array{0: string, 1: int, 2: string}> */
    public static function provideRender() : array
    {
        return [
            'reindent-to-1' => [
                "/**\n * Foo.\n * @return void\n */",
                1,
                "/**\n     * Foo.\n     * @return void\n     */",
            ],
            'reindent-to-2' => [
                "/**\n * Foo.\n */",
                2,
                "/**\n         * Foo.\n         */",
            ],
            'reindent-to-0' => [
                "/**\n     * Foo.\n     */",
                0,
                "/**\n * Foo.\n */",
            ],
        ];
    }
}
