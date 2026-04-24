<?php
declare(strict_types=1);

namespace PhpStyler\Format\Vendor;

use PhpStyler\Styler;
use PhpStyler\Token;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class SymfonyFormatTest extends TestCase
{
    private Styler $styler;

    protected function setUp() : void
    {
        $this->styler = new Styler(new SymfonyFormat());
    }

    #[DataProvider('provide')]
    public function test(string $code, string $expect) : void
    {
        $actual = ($this->styler)($code);
        $this->assertSame($expect, $actual);
    }

    public function testStylesOverrideMergesWithSymfonyDefaults() : void
    {
        $styler = new Styler(new SymfonyFormat(
            styles: [
                Token\TReturn::class => ['spaceAfter' => false],
            ],
        ));

        $code = <<<'CODE'
            <?php
            function foo() {
                $x = 1;
                return($x);
            }
            CODE;

        $actual = $styler($code);
        // Still blank line before return (default Symfony style)
        $this->assertStringContainsString("\n\n    return", $actual);
    }

    /** @return array<string, array{0: string, 1: string}> */
    public static function provide() : array
    {
        return [
            'short-array-syntax' => [
                <<<'CODE'
                <?php
                $x = array(1, 2, 3);
                CODE,
                <<<'EXPECT'
                <?php
                $x = [1, 2, 3];

                EXPECT,
            ],
            'short-list-syntax' => [
                <<<'CODE'
                <?php
                list($a, $b) = $c;
                CODE,
                <<<'EXPECT'
                <?php
                [$a, $b] = $c;

                EXPECT,
            ],
            'else-if-to-elseif' => [
                <<<'CODE'
                <?php
                if ($a) {
                    foo();
                } else if ($b) {
                    bar();
                }
                CODE,
                <<<'EXPECT'
                <?php
                if ($a) {
                    foo();
                } elseif ($b) {
                    bar();
                }

                EXPECT,
            ],
            'single-quotes' => [
                <<<'CODE'
                <?php
                $x = "hello";
                CODE,
                <<<'EXPECT'
                <?php
                $x = 'hello';

                EXPECT,
            ],
            'continue-to-break' => [
                <<<'CODE'
                <?php
                switch ($x) {
                    case 1:
                        continue;
                }
                CODE,
                <<<'EXPECT'
                <?php
                switch ($x) {
                    case 1:
                        break;
                }

                EXPECT,
            ],
            'yoda-conditions' => [
                <<<'CODE'
                <?php
                if ($x === 1) {
                    foo();
                }
                CODE,
                <<<'EXPECT'
                <?php
                if (1 === $x) {
                    foo();
                }

                EXPECT,
            ],
            'no-concatenation-spacing' => [
                <<<'CODE'
                <?php
                $x = $a . $b;
                CODE,
                <<<'EXPECT'
                <?php
                $x = $a.$b;

                EXPECT,
            ],
            'no-return-type-colon-spacing' => [
                <<<'CODE'
                <?php
                function foo() : int
                {
                    return 1;
                }
                CODE,
                <<<'EXPECT'
                <?php
                function foo(): int
                {
                    return 1;
                }

                EXPECT,
            ],
            'blank-line-before-return' => [
                <<<'CODE'
                <?php
                function foo()
                {
                    $x = 1;
                    return $x;
                }
                CODE,
                <<<'EXPECT'
                <?php
                function foo()
                {
                    $x = 1;

                    return $x;
                }

                EXPECT,
            ],
            'explicit-variable-interpolation' => [
                <<<'CODE'
                <?php
                $x = "hello $name world";
                CODE,
                <<<'EXPECT'
                <?php
                $x = "hello {$name} world";

                EXPECT,
            ],
            'skip-repeated-semicolons' => [
                <<<'CODE'
                <?php
                $x = 1;;
                CODE,
                <<<'EXPECT'
                <?php
                $x = 1;

                EXPECT,
            ],
        ];
    }
}
