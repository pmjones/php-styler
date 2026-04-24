<?php
declare(strict_types=1);

namespace PhpStyler\Rule\TokenRule;

use PhpStyler\Format\DeclarationFormat;
use PhpStyler\Rule\LineRule\RemoveTrailingBlankLines;
use PhpStyler\Styler;
use PhpStyler\Token\AToken;
use PhpStyler\Token\TLineBreak;
use PhpStyler\Token\TSpace;
use PhpStyler\Token\TUnqualifiedName;
use PhpStyler\Token\TUse;
use PhpStyler\Token\TUseClosingBrace;
use PhpStyler\Token\TUseEndSemicolon;
use PhpStyler\Token\TUseOpeningBrace;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ExpandGroupedImportsTest extends TestCase
{
    /** @return array<string, array{0: string, 1: string}> */
    public static function provide() : array
    {
        return [
            'basic' => [
                <<<'CODE'
                <?php
                use Foo\{Bar, Baz};
                new Bar();
                new Baz();
                CODE,
                <<<'EXPECT'
                <?php
                use Foo\Bar;
                use Foo\Baz;

                new Bar();
                new Baz();

                EXPECT,
            ],
            'with-alias' => [
                <<<'CODE'
                <?php
                use Foo\{Bar, Baz as B};
                new Bar();
                new B();
                CODE,
                <<<'EXPECT'
                <?php
                use Foo\Bar;
                use Foo\Baz as B;

                new Bar();
                new B();

                EXPECT,
            ],
            'not-grouped' => [
                <<<'CODE'
                <?php
                use Foo\Bar;
                new Bar();
                CODE,
                <<<'EXPECT'
                <?php
                use Foo\Bar;

                new Bar();

                EXPECT,
            ],
            'const-group' => [
                <<<'CODE'
                <?php
                use const Foo\{BAR, BAZ};
                echo BAR;
                echo BAZ;
                CODE,
                <<<'EXPECT'
                <?php
                use const Foo\BAR;
                use const Foo\BAZ;

                echo BAR;
                echo BAZ;

                EXPECT,
            ],
            'function-group' => [
                <<<'CODE'
                <?php
                use function Foo\{bar, baz};
                bar();
                baz();
                CODE,
                <<<'EXPECT'
                <?php
                use function Foo\bar;
                use function Foo\baz;

                bar();
                baz();

                EXPECT,
            ],
            'per-item-function-in-group' => [
                <<<'CODE'
                <?php
                use Foo\{Bar, function baz};
                new Bar();
                baz();
                CODE,
                <<<'EXPECT'
                <?php
                use Foo\Bar;

                use function Foo\baz;

                new Bar();
                baz();

                EXPECT,
            ],
            'per-item-const-in-group' => [
                <<<'CODE'
                <?php
                use Foo\{Bar, const BAZ};
                new Bar();
                echo BAZ;
                CODE,
                <<<'EXPECT'
                <?php
                use Foo\Bar;

                use const Foo\BAZ;

                new Bar();
                echo BAZ;

                EXPECT,
            ],
        ];
    }

    #[DataProvider('provide')]
    public function test(string $code, string $expect) : void
    {
        $styler = new Styler(
            new DeclarationFormat(rules: [
                ExpandGroupedImports::class,
                RemoveTrailingBlankLines::class,
            ]),
        );

        $actual = $styler($code);
        $this->assertSame($expect, $actual);
    }

    /**
     * Resilience: a grouped use missing its trailing semicolon / closing
     * brace is passed through unchanged instead of being consumed as an
     * expansion.
     */
    public function testMalformedGroupedUseMissingSemicolonPassesThrough() : void
    {
        // `use Foo\{Bar}` with the opening brace and closer but NO
        // terminating semicolon in the stream after the closer.
        $tokens = [
            new TUse(AToken::SYNTHETIC, 'use'),
            new TSpace(AToken::SYNTHETIC, ' '),
            new TUnqualifiedName(AToken::SYNTHETIC, 'Foo'),
            new TUseOpeningBrace(AToken::SYNTHETIC, '{'),
            new TUnqualifiedName(AToken::SYNTHETIC, 'Bar'),
            new TUseClosingBrace(AToken::SYNTHETIC, '}'),

            // no TUseEndSemicolon
            new TLineBreak(AToken::SYNTHETIC, "\n"),
        ];

        $rule = new ExpandGroupedImports();
        $result = $rule->apply($tokens);

        // Rule should have emitted original TUse unchanged because the
        // lookahead failed to find a terminating semicolon.
        $this->assertSame($tokens[0], $result[0]);
    }

    /**
     * Resilience: a grouped use with empty braces `use Foo\{};` has no
     * segments to expand, so it's passed through unchanged. Use
     * non-SYNTHETIC token ids so `isIgnorable()` returns false — the
     * inner-loop semicolon detection depends on that.
     */
    public function testMalformedGroupedUseEmptySegmentsPassesThrough() : void
    {
        $tokens = [
            new TUse(T_USE, 'use'),
            new TSpace(AToken::SYNTHETIC, ' '),
            new TUnqualifiedName(T_STRING, 'Foo'),
            new TUseOpeningBrace(ord('{'), '{'),

            // no name tokens between braces
            new TUseClosingBrace(ord('}'), '}'),
            new TUseEndSemicolon(ord(';'), ';'),
        ];

        $rule = new ExpandGroupedImports();
        $result = $rule->apply($tokens);

        // Empty-segments branch returns the original TUse unchanged;
        // surrounding tokens continue through untouched.
        $this->assertSame($tokens[0], $result[0]);
        $this->assertSame(count($tokens), count($result));
    }
}
