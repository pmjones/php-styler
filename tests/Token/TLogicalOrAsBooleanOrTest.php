<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Format\DeclarationFormat;
use PhpStyler\Rule\RemoveTrailingBlankLines;
use PhpStyler\Styler;
use PHPUnit\Framework\TestCase;

class TLogicalOrAsBooleanOrTest extends TestCase
{
    /**
     * @dataProvider provide
     */
    public function test(string $code, string $expect) : void
    {
        $styler = new Styler(
            new DeclarationFormat(
                parseAs: [TLogicalOr::class => TLogicalOrAsBooleanOr::class],
                rules: [RemoveTrailingBlankLines::class],
            ),
        );
        $actual = $styler($code);
        $this->assertSame($expect, $actual);
    }

    /** @return array<string, array{0: string, 1: string}> */
    public static function provide() : array
    {
        return [
            'or-to-boolean-or' => [
                <<<'CODE'
                <?php
                $a = $b or $c;
                CODE,
                <<<'EXPECT'
                <?php
                $a = $b || $c;

                EXPECT,
            ],
            'already-boolean-or' => [
                <<<'CODE'
                <?php
                $a = $b || $c;
                CODE,
                <<<'EXPECT'
                <?php
                $a = $b || $c;

                EXPECT,
            ],
        ];
    }
}
