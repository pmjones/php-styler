<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Format\DeclarationFormat;
use PhpStyler\Styler;
use PHPUnit\Framework\TestCase;

class AddExitParenthesesTest extends TestCase
{
    /**
     * @dataProvider provide
     */
    public function test(string $code, string $expect) : void
    {
        $styler = new Styler(
            new DeclarationFormat(rules: [
                AddExitParentheses::class,
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
            'bare-exit' => [
                <<<'CODE'
                <?php
                exit;
                CODE,
                <<<'EXPECT'
                <?php
                exit();

                EXPECT,
            ],
            'bare-die' => [
                <<<'CODE'
                <?php
                die;
                CODE,
                <<<'EXPECT'
                <?php
                die();

                EXPECT,
            ],
            'exit-already-has-parens' => [
                <<<'CODE'
                <?php
                exit(1);
                CODE,
                <<<'EXPECT'
                <?php
                exit(1);

                EXPECT,
            ],
            'exit-with-arg-parens' => [
                <<<'CODE'
                <?php
                exit(0);
                CODE,
                <<<'EXPECT'
                <?php
                exit(0);

                EXPECT,
            ],
        ];
    }
}
