<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Format\ExtendedFormat;
use PhpStyler\Styler;
use PHPUnit\Framework\TestCase;

class ConvertDoubleToSingleQuoteTest extends TestCase
{
    /**
     * @dataProvider provide
     */
    public function test(string $code, string $expect) : void
    {
        $styler = new Styler(
            new ExtendedFormat(rules: [
                ConvertDoubleToSingleQuote::class,
                RemoveTrailingBlankLines::class,
            ]),
        );
        $actual = $styler($code);
        $this->assertSame($expect, $actual);
    }

    /** @return array<string, array{0: string, 1: string}> */
    public static function provide() : array
    {
        return [
            'simple-string' => [
                <<<'CODE'
                <?php
                $x = "hello";
                CODE,
                <<<'EXPECT'
                <?php
                $x = 'hello';

                EXPECT,
            ],
            'empty-string' => [
                <<<'CODE'
                <?php
                $x = "";
                CODE,
                <<<'EXPECT'
                <?php
                $x = '';

                EXPECT,
            ],
            'with-escaped-double-quote' => [
                <<<'CODE'
                <?php
                $x = "he said \"hi\"";
                CODE,
                <<<'EXPECT'
                <?php
                $x = 'he said "hi"';

                EXPECT,
            ],
            'with-single-quote-skip' => [
                <<<'CODE'
                <?php
                $x = "it's";
                CODE,
                <<<'EXPECT'
                <?php
                $x = "it's";

                EXPECT,
            ],
            'with-newline-skip' => [
                <<<'CODE'
                <?php
                $x = "hello\nworld";
                CODE,
                <<<'EXPECT'
                <?php
                $x = "hello\nworld";

                EXPECT,
            ],
            'with-tab-skip' => [
                <<<'CODE'
                <?php
                $x = "foo\tbar";
                CODE,
                <<<'EXPECT'
                <?php
                $x = "foo\tbar";

                EXPECT,
            ],
            'already-single-quote' => [
                <<<'CODE'
                <?php
                $x = 'hello';
                CODE,
                <<<'EXPECT'
                <?php
                $x = 'hello';

                EXPECT,
            ],
            'with-backslash' => [
                <<<'CODE'
                <?php
                $x = "back\\slash";
                CODE,
                <<<'EXPECT'
                <?php
                $x = 'back\\slash';

                EXPECT,
            ],
            'with-dollar-escape-skip' => [
                <<<'CODE'
                <?php
                $x = "price is \$5";
                CODE,
                <<<'EXPECT'
                <?php
                $x = "price is \$5";

                EXPECT,
            ],
        ];
    }
}
