<?php
declare(strict_types=1);

namespace PhpStyler\Rule\TokenRule;

use PhpStyler\Format\DeclarationFormat;
use PhpStyler\Rule\LineRule\RemoveTrailingBlankLines;
use PhpStyler\Styler;
use PhpStyler\Token\AToken;
use PhpStyler\Token\TArgsClosingParen;
use PhpStyler\Token\TExpressionClosingParen;
use PhpStyler\Token\TExpressionOpeningParen;
use PhpStyler\Token\TPrint;
use PhpStyler\Token\TSemicolon;
use PhpStyler\Token\TStringLiteral;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class RemoveLanguageConstructParensTest extends TestCase
{
    /** @return array<string, array{0: string, 1: string}> */
    public static function provide() : array
    {
        return [
            'echo-parens' => [
                <<<'CODE'
                <?php
                echo($x);
                CODE,
                <<<'EXPECT'
                <?php
                echo $x;

                EXPECT,
            ],
            'return-parens' => [
                <<<'CODE'
                <?php
                function foo() {
                    return($x);
                }
                CODE,
                <<<'EXPECT'
                <?php
                function foo()
                {
                    return $x;
                }

                EXPECT,
            ],
            'include-parens' => [
                <<<'CODE'
                <?php
                include($file);
                CODE,
                <<<'EXPECT'
                <?php
                include $file;

                EXPECT,
            ],
            'already-no-parens' => [
                <<<'CODE'
                <?php
                echo $x;
                CODE,
                <<<'EXPECT'
                <?php
                echo $x;

                EXPECT,
            ],
            'return-no-argument' => [
                <<<'CODE'
                <?php
                function foo() {
                    return;
                }
                CODE,
                <<<'EXPECT'
                <?php
                function foo()
                {
                    return;
                }

                EXPECT,
            ],
            'complex-expression' => [
                <<<'CODE'
                <?php
                echo($a + $b);
                CODE,
                <<<'EXPECT'
                <?php
                echo $a + $b;

                EXPECT,
            ],
            'print-parens' => [
                <<<'CODE'
                <?php
                print($x);
                CODE,
                <<<'EXPECT'
                <?php
                print $x;

                EXPECT,
            ],
            'require-parens' => [
                <<<'CODE'
                <?php
                require($file);
                CODE,
                <<<'EXPECT'
                <?php
                require $file;

                EXPECT,
            ],
            'require-once-parens' => [
                <<<'CODE'
                <?php
                require_once($file);
                CODE,
                <<<'EXPECT'
                <?php
                require_once $file;

                EXPECT,
            ],
            'echo-no-parens-no-change' => [
                <<<'CODE'
                <?php
                echo $x;
                CODE,
                <<<'EXPECT'
                <?php
                echo $x;

                EXPECT,
            ],
            'echo-parens-followed-by-concat-not-removed' => [
                <<<'CODE'
                <?php
                echo ($x) . $y;
                CODE,
                <<<'EXPECT'
                <?php
                echo ($x) . $y;

                EXPECT,
            ],
            'return-parens-followed-by-arithmetic-not-removed' => [
                <<<'CODE'
                <?php
                function foo() {
                    return ($a) + $b;
                }
                CODE,
                <<<'EXPECT'
                <?php
                function foo()
                {
                    return ($a) + $b;
                }

                EXPECT,
            ],
            'print-parens-removed-before-semicolon' => [
                <<<'CODE'
                <?php
                $x = print($y);
                CODE,
                <<<'EXPECT'
                <?php
                $x = print $y;

                EXPECT,
            ],
            'print-as-method-call-keeps-parens' => [
                <<<'CODE'
                <?php
                $foo->print("bar");
                CODE,
                <<<'EXPECT'
                <?php
                $foo->print("bar");

                EXPECT,
            ],
            'print-as-static-method-call-keeps-parens' => [
                <<<'CODE'
                <?php
                Foo::print("baz");
                CODE,
                <<<'EXPECT'
                <?php
                Foo::print("baz");

                EXPECT,
            ],
        ];
    }

    #[DataProvider('provide')]
    public function test(string $code, string $expect) : void
    {
        $styler = new Styler(
            new DeclarationFormat(rules: [
                RemoveLanguageConstructParens::class,
                RemoveTrailingBlankLines::class,
            ]),
        );

        $actual = $styler($code);
        $this->assertSame($expect, $actual);
    }

    /**
     * Resilience: if an upstream rule has produced a stream where
     * `TExpressionOpeningParen::closingToken` points to something that
     * isn't a `TExpressionClosingParen`, pass the construct through
     * unchanged instead of transforming it into something malformed.
     */
    public function testMalformedClosingTokenWrongTypePassesThrough() : void
    {
        $opener = new TExpressionOpeningParen(ord('('), '(', 1, 0);

        // Paired with a wrong-type closer — simulates a prior rule that
        // mis-rewrote the opener's link.
        $wrongCloser = new TArgsClosingParen(ord(')'), ')', 1, 0);
        AToken::pair($opener, $wrongCloser);

        $tokens = [
            new TPrint(T_PRINT, 'print', 1, 0),
            $opener,
            new TStringLiteral(T_CONSTANT_ENCAPSED_STRING, '"hello"', 1, 0),
            $wrongCloser,
            new TSemicolon(ord(';'), ';', 1, 0),
        ];

        $rule = new RemoveLanguageConstructParens();
        $result = $rule->apply($tokens);

        // Same length and types as input — rule left everything alone.
        $this->assertCount(count($tokens), $result);
        $this->assertSame($tokens[0], $result[0]);
        $this->assertInstanceOf(TExpressionOpeningParen::class, $result[1]);
    }

    /**
     * Resilience: if the closer linked from the opener is a correct
     * TExpressionClosingParen type but is not actually present in the
     * $tokens array being processed (indexMap lookup returns null),
     * pass the construct through unchanged.
     */
    public function testMalformedClosingTokenNotInArrayPassesThrough() : void
    {
        $opener = new TExpressionOpeningParen(ord('('), '(', 1, 0);

        // Correct-type closer, but deliberately NOT added to $tokens —
        // simulates an orphaned pair link from an earlier transform.
        $orphanCloser = new TExpressionClosingParen(ord(')'), ')', 1, 0);
        AToken::pair($opener, $orphanCloser);

        $tokens = [
            new TPrint(T_PRINT, 'print', 1, 0),
            $opener,
            new TStringLiteral(T_CONSTANT_ENCAPSED_STRING, '"hello"', 1, 0),

            // orphan closer NOT in this array
            new TSemicolon(ord(';'), ';', 1, 0),
        ];

        $rule = new RemoveLanguageConstructParens();
        $result = $rule->apply($tokens);

        $this->assertCount(count($tokens), $result);
        $this->assertSame($tokens[0], $result[0]);
        $this->assertInstanceOf(TExpressionOpeningParen::class, $result[1]);
    }
}
