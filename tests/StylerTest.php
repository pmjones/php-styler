<?php
declare(strict_types=1);

namespace PhpStyler;

use PhpStyler\Rule\LineRule\MergeParenBrace;
use PhpStyler\Rule\LineRule\NormalizeTrailingCommas;
use PhpStyler\Rule\LineRule\RemoveTrailingBlankLines;
use PhpStyler\Rule\TokenRule\ConvertToShortArraySyntax;
use PhpStyler\Rule\TokenRule\ConvertToShortListSyntax;
use PhpStyler\Rule\TokenRule\ConvertVarToPublic;
use PhpStyler\Rule\TokenRule\ExpandGroupedImports;
use PhpStyler\Rule\TokenRule\InsertPublicVisibility;
use PhpStyler\Rule\TokenRule\NormalizeModifierOrder;
use PhpStyler\TestFormat;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class StylerTest extends TestCase
{
    /** @return array<string, array{0: string, 1: string}> */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'simple-assignment' => [
                <<<'CODE'
                <?php $foo = "bar";
                CODE,
                <<<'EXPECT'
                <?php $foo = "bar";

                EXPECT,
            ],
            'function-call' => [
                <<<'CODE'
                <?php echo foo($a, $b);
                CODE,
                <<<'EXPECT'
                <?php echo foo($a, $b);

                EXPECT,
            ],
            'class-with-method' => [
                <<<'CODE'
                <?php class Foo { public function bar() : void { $baz = 1; } }
                CODE,
                <<<'EXPECT'
                <?php class Foo
                {
                    public function bar() : void
                    {
                        $baz = 1;
                    }
                }

                EXPECT,
            ],
            'blank-line-preservation' => [
                <<<'CODE'
                <?php
                $a = 1;

                $b = 2;
                CODE,
                <<<'EXPECT'
                <?php
                $a = 1;

                $b = 2;

                EXPECT,
            ],
            'open-tag' => [
                <<<'CODE'
                <?php

                CODE,
                <<<'EXPECT'
                <?php

                EXPECT,
            ],
            'cast-int' => [
                <<<'CODE'
                <?php $n = (int) $str;
                CODE,
                <<<'EXPECT'
                <?php $n = (int) $str;

                EXPECT,
            ],
            'cast-string' => [
                <<<'CODE'
                <?php $s = (string) $val;
                CODE,
                <<<'EXPECT'
                <?php $s = (string) $val;

                EXPECT,
            ],
            'cast-array' => [
                <<<'CODE'
                <?php $a = (array) $obj;
                CODE,
                <<<'EXPECT'
                <?php $a = (array) $obj;

                EXPECT,
            ],
            'cast-bool' => [
                <<<'CODE'
                <?php $b = (bool) $val;
                CODE,
                <<<'EXPECT'
                <?php $b = (bool) $val;

                EXPECT,
            ],
            'cast-float' => [
                <<<'CODE'
                <?php $f = (float) $val;
                CODE,
                <<<'EXPECT'
                <?php $f = (float) $val;

                EXPECT,
            ],
            'cast-object' => [
                <<<'CODE'
                <?php $o = (object) $arr;
                CODE,
                <<<'EXPECT'
                <?php $o = (object) $arr;

                EXPECT,
            ],
            'cast-unset' => [
                <<<'CODE'
                <?php $u = (unset) $val;
                CODE,
                <<<'EXPECT'
                <?php $u = (unset) $val;

                EXPECT,
            ],
            'string-concat' => [
                <<<'CODE'
                <?php $msg = "hello" . " " . "world";
                CODE,
                <<<'EXPECT'
                <?php $msg = "hello" . " " . "world";

                EXPECT,
            ],
            'method-call' => [
                <<<'CODE'
                <?php $obj->doStuff($a, $b);
                CODE,
                <<<'EXPECT'
                <?php $obj->doStuff($a, $b);

                EXPECT,
            ],
            'static-call' => [
                <<<'CODE'
                <?php Foo::bar($x);
                CODE,
                <<<'EXPECT'
                <?php Foo::bar($x);

                EXPECT,
            ],
            'array-access' => [
                <<<'CODE'
                <?php $val = $arr[$key];
                CODE,
                <<<'EXPECT'
                <?php $val = $arr[$key];

                EXPECT,
            ],
            'negative' => [
                <<<'CODE'
                <?php $n = -1;
                CODE,
                <<<'EXPECT'
                <?php $n = -1;

                EXPECT,
            ],
            'ternary' => [
                <<<'CODE'
                <?php $x = $a ? $b : $c;
                CODE,
                <<<'EXPECT'
                <?php $x = $a ? $b : $c;

                EXPECT,
            ],
            'ternary-short' => [
                <<<'CODE'
                <?php $x = $a ?: $b;
                CODE,
                <<<'EXPECT'
                <?php $x = $a ?: $b;

                EXPECT,
            ],
            'null-coalesce' => [
                <<<'CODE'
                <?php $x = $a ?? $b;
                CODE,
                <<<'EXPECT'
                <?php $x = $a ?? $b;

                EXPECT,
            ],
            'spaceship' => [
                <<<'CODE'
                <?php $c = $a <=> $b;
                CODE,
                <<<'EXPECT'
                <?php $c = $a <=> $b;

                EXPECT,
            ],
            'instanceof' => [
                <<<'CODE'
                <?php $ok = $obj instanceof Foo;
                CODE,
                <<<'EXPECT'
                <?php $ok = $obj instanceof Foo;

                EXPECT,
            ],
            'new-instance' => [
                <<<'CODE'
                <?php $obj = new Foo($a, $b);
                CODE,
                <<<'EXPECT'
                <?php $obj = new Foo($a, $b);

                EXPECT,
            ],
            'arrow-fn' => [
                <<<'CODE'
                <?php $fn = fn(int $x) => $x * 2;
                CODE,
                <<<'EXPECT'
                <?php $fn = fn (int $x) => $x * 2;

                EXPECT,
            ],
            'named-arg' => [
                <<<'CODE'
                <?php foo(name: $val);
                CODE,
                <<<'EXPECT'
                <?php foo(name: $val);

                EXPECT,
            ],
            'spread' => [
                <<<'CODE'
                <?php foo(...$args);
                CODE,
                <<<'EXPECT'
                <?php foo(...$args);

                EXPECT,
            ],
            'increment' => [
                <<<'CODE'
                <?php $i++; ++$j;
                CODE,
                <<<'EXPECT'
                <?php $i ++;
                ++ $j;

                EXPECT,
            ],
            'boolean-ops' => [
                <<<'CODE'
                <?php $ok = $a && $b || !$c;
                CODE,
                <<<'EXPECT'
                <?php $ok = $a && $b || ! $c;

                EXPECT,
            ],
            'assign-ops' => [
                <<<'CODE'
                <?php $x += 1; $y .= "s"; $z ??= 0;
                CODE,
                <<<'EXPECT'
                <?php $x += 1;
                $y .= "s";
                $z ??= 0;

                EXPECT,
            ],
            'suppress' => [
                <<<'CODE'
                <?php @unlink($f);
                CODE,
                <<<'EXPECT'
                <?php @unlink($f);

                EXPECT,
            ],
            'chained-call' => [
                <<<'CODE'
                <?php $obj->foo()->bar()->baz();
                CODE,
                <<<'EXPECT'
                <?php $obj->foo()->bar()->baz();

                EXPECT,
            ],
            'static-prop' => [
                <<<'CODE'
                <?php Foo::$bar;
                CODE,
                <<<'EXPECT'
                <?php Foo::$bar;

                EXPECT,
            ],
            'array-short' => [
                <<<'CODE'
                <?php $a = [1, 2, 3];
                CODE,
                <<<'EXPECT'
                <?php $a = [1, 2, 3];

                EXPECT,
            ],
            'string-interp' => [
                <<<'CODE'
                <?php $s = "hello {$name}";
                CODE,
                <<<'EXPECT'
                <?php $s = "hello {$name}";

                EXPECT,
            ],
            'declare' => [
                <<<'CODE'
                <?php declare(strict_types=1);
                CODE,
                <<<'EXPECT'
                <?php declare(strict_types=1);

                EXPECT,
            ],
            'echo-multi' => [
                <<<'CODE'
                <?php echo $a, $b, $c;
                CODE,
                <<<'EXPECT'
                <?php echo $a, $b, $c;

                EXPECT,
            ],
            'catch-paren' => [
                <<<'CODE'
                <?php try { risky(); } catch (\Exception $e) { log($e); }
                CODE,
                <<<'EXPECT'
                <?php try {
                    risky();
                } catch (\Exception $e) {
                    log($e);
                }

                EXPECT,
            ],
            'nullable-param' => [
                <<<'CODE'
                <?php function f(?string $s) : void {}
                CODE,
                <<<'EXPECT'
                <?php function f(?string $s) : void
                {
                }

                EXPECT,
            ],
            'variadic-param' => [
                <<<'CODE'
                <?php function f(int ...$args) {}
                CODE,
                <<<'EXPECT'
                <?php function f(int ...$args)
                {
                }

                EXPECT,
            ],
            'ref-param' => [
                <<<'CODE'
                <?php function f(int &$x) {}
                CODE,
                <<<'EXPECT'
                <?php function f(int &$x)
                {
                }

                EXPECT,
            ],

            // Arithmetic operators
            'arith-add' => [
                <<<'CODE'
                <?php $x = $a + $b;
                CODE,
                <<<'EXPECT'
                <?php $x = $a + $b;

                EXPECT,
            ],
            'arith-sub' => [
                <<<'CODE'
                <?php $x = $a - $b;
                CODE,
                <<<'EXPECT'
                <?php $x = $a - $b;

                EXPECT,
            ],
            'arith-mul' => [
                <<<'CODE'
                <?php $x = $a * $b;
                CODE,
                <<<'EXPECT'
                <?php $x = $a * $b;

                EXPECT,
            ],
            'arith-div' => [
                <<<'CODE'
                <?php $x = $a / $b;
                CODE,
                <<<'EXPECT'
                <?php $x = $a / $b;

                EXPECT,
            ],
            'arith-mod' => [
                <<<'CODE'
                <?php $x = $a % $b;
                CODE,
                <<<'EXPECT'
                <?php $x = $a % $b;

                EXPECT,
            ],
            'arith-pow' => [
                <<<'CODE'
                <?php $x = $a ** $b;
                CODE,
                <<<'EXPECT'
                <?php $x = $a ** $b;

                EXPECT,
            ],
            'unary-plus' => [
                <<<'CODE'
                <?php $x = +$a;
                CODE,
                <<<'EXPECT'
                <?php $x = +$a;

                EXPECT,
            ],

            // Comparison operators
            'cmp-equal' => [
                <<<'CODE'
                <?php $x = $a == $b;
                CODE,
                <<<'EXPECT'
                <?php $x = $a == $b;

                EXPECT,
            ],
            'cmp-identical' => [
                <<<'CODE'
                <?php $x = $a === $b;
                CODE,
                <<<'EXPECT'
                <?php $x = $a === $b;

                EXPECT,
            ],
            'cmp-not-equal' => [
                <<<'CODE'
                <?php $x = $a != $b;
                CODE,
                <<<'EXPECT'
                <?php $x = $a != $b;

                EXPECT,
            ],
            'cmp-not-identical' => [
                <<<'CODE'
                <?php $x = $a !== $b;
                CODE,
                <<<'EXPECT'
                <?php $x = $a !== $b;

                EXPECT,
            ],
            'cmp-lt' => [
                <<<'CODE'
                <?php $x = $a < $b;
                CODE,
                <<<'EXPECT'
                <?php $x = $a < $b;

                EXPECT,
            ],
            'cmp-gt' => [
                <<<'CODE'
                <?php $x = $a > $b;
                CODE,
                <<<'EXPECT'
                <?php $x = $a > $b;

                EXPECT,
            ],
            'cmp-lte' => [
                <<<'CODE'
                <?php $x = $a <= $b;
                CODE,
                <<<'EXPECT'
                <?php $x = $a <= $b;

                EXPECT,
            ],
            'cmp-gte' => [
                <<<'CODE'
                <?php $x = $a >= $b;
                CODE,
                <<<'EXPECT'
                <?php $x = $a >= $b;

                EXPECT,
            ],

            // Bitwise operators
            'bit-or' => [
                <<<'CODE'
                <?php $x = $a | $b;
                CODE,
                <<<'EXPECT'
                <?php $x = $a | $b;

                EXPECT,
            ],
            'bit-xor' => [
                <<<'CODE'
                <?php $x = $a ^ $b;
                CODE,
                <<<'EXPECT'
                <?php $x = $a ^ $b;

                EXPECT,
            ],
            'bit-not' => [
                <<<'CODE'
                <?php $x = ~$a;
                CODE,
                <<<'EXPECT'
                <?php $x = ~$a;

                EXPECT,
            ],
            'bit-shift-left' => [
                <<<'CODE'
                <?php $x = $a << 2;
                CODE,
                <<<'EXPECT'
                <?php $x = $a << 2;

                EXPECT,
            ],
            'bit-shift-right' => [
                <<<'CODE'
                <?php $x = $a >> 2;
                CODE,
                <<<'EXPECT'
                <?php $x = $a >> 2;

                EXPECT,
            ],

            // Logical keyword operators
            'logical-and' => [
                <<<'CODE'
                <?php $x = $a and $b;
                CODE,
                <<<'EXPECT'
                <?php $x = $a and $b;

                EXPECT,
            ],
            'logical-or' => [
                <<<'CODE'
                <?php $x = $a or $b;
                CODE,
                <<<'EXPECT'
                <?php $x = $a or $b;

                EXPECT,
            ],
            'logical-xor' => [
                <<<'CODE'
                <?php $x = $a xor $b;
                CODE,
                <<<'EXPECT'
                <?php $x = $a xor $b;

                EXPECT,
            ],

            // More assignment operators
            'assign-sub' => [
                <<<'CODE'
                <?php $x -= 1;
                CODE,
                <<<'EXPECT'
                <?php $x -= 1;

                EXPECT,
            ],
            'assign-mul' => [
                <<<'CODE'
                <?php $x *= 2;
                CODE,
                <<<'EXPECT'
                <?php $x *= 2;

                EXPECT,
            ],
            'assign-div' => [
                <<<'CODE'
                <?php $x /= 2;
                CODE,
                <<<'EXPECT'
                <?php $x /= 2;

                EXPECT,
            ],
            'assign-mod' => [
                <<<'CODE'
                <?php $x %= 2;
                CODE,
                <<<'EXPECT'
                <?php $x %= 2;

                EXPECT,
            ],
            'assign-pow' => [
                <<<'CODE'
                <?php $x **= 2;
                CODE,
                <<<'EXPECT'
                <?php $x **= 2;

                EXPECT,
            ],
            'assign-and' => [
                <<<'CODE'
                <?php $x &= 1;
                CODE,
                <<<'EXPECT'
                <?php $x &= 1;

                EXPECT,
            ],
            'assign-or' => [
                <<<'CODE'
                <?php $x |= 1;
                CODE,
                <<<'EXPECT'
                <?php $x |= 1;

                EXPECT,
            ],
            'assign-xor' => [
                <<<'CODE'
                <?php $x ^= 1;
                CODE,
                <<<'EXPECT'
                <?php $x ^= 1;

                EXPECT,
            ],
            'assign-shift-left' => [
                <<<'CODE'
                <?php $x <<= 1;
                CODE,
                <<<'EXPECT'
                <?php $x <<= 1;

                EXPECT,
            ],
            'assign-shift-right' => [
                <<<'CODE'
                <?php $x >>= 1;
                CODE,
                <<<'EXPECT'
                <?php $x >>= 1;

                EXPECT,
            ],

            // Control structures
            'if-elseif-else' => [
                <<<'CODE'
                <?php if ($a) { $x = 1; } elseif ($b) { $x = 2; } else { $x = 3; }
                CODE,
                <<<'EXPECT'
                <?php if ($a) {
                    $x = 1;
                } elseif ($b) {
                    $x = 2;
                } else {
                    $x = 3;
                }

                EXPECT,
            ],
            'for-loop' => [
                <<<'CODE'
                <?php for ($i = 0; $i < 10; $i++) { echo $i; }
                CODE,
                <<<'EXPECT'
                <?php for ($i = 0; $i < 10; $i ++) {
                    echo $i;
                }

                EXPECT,
            ],
            'foreach' => [
                <<<'CODE'
                <?php foreach ($arr as $k => $v) { echo $v; }
                CODE,
                <<<'EXPECT'
                <?php foreach ($arr as $k => $v) {
                    echo $v;
                }

                EXPECT,
            ],
            'while' => [
                <<<'CODE'
                <?php while ($x > 0) { $x--; }
                CODE,
                <<<'EXPECT'
                <?php while ($x > 0) {
                    $x --;
                }

                EXPECT,
            ],
            'do-while' => [
                <<<'CODE'
                <?php do { $i++; } while ($i < 10);
                CODE,
                <<<'EXPECT'
                <?php do {
                    $i ++;
                } while ($i < 10);

                EXPECT,
            ],
            'switch-case' => [
                <<<'CODE'
                <?php switch ($x) { case 1: break; case 2: break; default: break; }
                CODE,
                <<<'EXPECT'
                <?php switch ($x) {
                    case 1:
                        break;
                    case 2:
                        break;
                    default:
                        break;
                }

                EXPECT,
            ],
            'try-catch-finally' => [
                <<<'CODE'
                <?php try { risky(); } catch (\Exception $e) { log($e); } finally { cleanup(); }
                CODE,
                <<<'EXPECT'
                <?php try {
                    risky();
                } catch (\Exception $e) {
                    log($e);
                } finally {
                    cleanup();
                }

                EXPECT,
            ],

            // Return / yield / throw
            'return' => [
                <<<'CODE'
                <?php function f() { return $x; }
                CODE,
                <<<'EXPECT'
                <?php function f()
                {
                    return $x;
                }

                EXPECT,
            ],
            'return-void' => [
                <<<'CODE'
                <?php function f() { return; }
                CODE,
                <<<'EXPECT'
                <?php function f()
                {
                    return;
                }

                EXPECT,
            ],
            'yield' => [
                <<<'CODE'
                <?php function f() { yield $x; }
                CODE,
                <<<'EXPECT'
                <?php function f()
                {
                    yield $x;
                }

                EXPECT,
            ],
            'yield-key-value' => [
                <<<'CODE'
                <?php function f() { yield $k => $v; }
                CODE,
                <<<'EXPECT'
                <?php function f()
                {
                    yield $k => $v;
                }

                EXPECT,
            ],
            'yield-from' => [
                <<<'CODE'
                <?php function f() { yield from $gen; }
                CODE,
                <<<'EXPECT'
                <?php function f()
                {
                    yield from $gen;
                }

                EXPECT,
            ],
            'throw-new' => [
                <<<'CODE'
                <?php function f() { throw new \Exception("err"); }
                CODE,
                <<<'EXPECT'
                <?php function f()
                {
                    throw new \Exception("err");
                }

                EXPECT,
            ],

            // Class features
            'const-class' => [
                <<<'CODE'
                <?php class Foo { const BAR = 1; }
                CODE,
                <<<'EXPECT'
                <?php class Foo
                {
                    public const BAR = 1;
                }

                EXPECT,
            ],
            'property' => [
                <<<'CODE'
                <?php class Foo { public int $bar = 0; }
                CODE,
                <<<'EXPECT'
                <?php class Foo
                {
                    public int $bar = 0;
                }

                EXPECT,
            ],
            'abstract-method' => [
                <<<'CODE'
                <?php abstract class Foo { abstract public function bar() : void; }
                CODE,
                <<<'EXPECT'
                <?php abstract class Foo
                {
                    abstract public function bar() : void;
                }

                EXPECT,
            ],
            'interface-method' => [
                <<<'CODE'
                <?php interface Foo { public function bar(int $x) : string; }
                CODE,
                <<<'EXPECT'
                <?php interface Foo
                {
                    public function bar(int $x) : string;
                }

                EXPECT,
            ],
            'extends-implements' => [
                <<<'CODE'
                <?php class Foo extends Bar implements Baz, Qux {}
                CODE,
                <<<'EXPECT'
                <?php class Foo extends Bar implements Baz, Qux
                {
                }

                EXPECT,
            ],
            'enum-cases' => [
                <<<'CODE'
                <?php enum Color { case Red; case Blue; }
                CODE,
                <<<'EXPECT'
                <?php enum Color
                {
                    case Red;

                    case Blue;
                }

                EXPECT,
            ],
            'enum-backed' => [
                <<<'CODE'
                <?php enum Color : string { case Red = "red"; }
                CODE,
                <<<'EXPECT'
                <?php enum Color : string
                {
                    case Red = "red";
                }

                EXPECT,
            ],
            'trait-def' => [
                <<<'CODE'
                <?php trait Foo { public function bar() : void {} }
                CODE,
                <<<'EXPECT'
                <?php trait Foo
                {
                    public function bar() : void
                    {
                    }
                }

                EXPECT,
            ],
            'use-trait' => [
                <<<'CODE'
                <?php class Foo { use Bar, Baz; }
                CODE,
                <<<'EXPECT'
                <?php class Foo
                {
                    use Bar;

                    use Baz;
                }

                EXPECT,
            ],

            // Closures
            'closure' => [
                <<<'CODE'
                <?php $fn = function ($x) use ($y) { return $x + $y; };
                CODE,
                <<<'EXPECT'
                <?php $fn = function ($x) use ($y) {
                    return $x + $y;
                };

                EXPECT,
            ],
            'closure-typed' => [
                <<<'CODE'
                <?php $fn = function (int $x) : int { return $x * 2; };
                CODE,
                <<<'EXPECT'
                <?php $fn = function (int $x) : int {
                    return $x * 2;
                };

                EXPECT,
            ],

            // Namespace / use
            'namespace' => [
                <<<'CODE'
                <?php namespace Foo\Bar;
                CODE,
                <<<'EXPECT'
                <?php namespace Foo\Bar;

                EXPECT,
            ],
            'use-single' => [
                <<<'CODE'
                <?php use Foo\Bar;
                CODE,
                <<<'EXPECT'
                <?php use Foo\Bar;

                EXPECT,
            ],
            'use-grouped' => [
                <<<'CODE'
                <?php use Foo\{Bar, Baz};
                CODE,
                <<<'EXPECT'
                <?php use Foo\Bar;
                use Foo\Baz;

                EXPECT,
            ],
            'use-function' => [
                <<<'CODE'
                <?php use function Foo\bar;
                CODE,
                <<<'EXPECT'
                <?php use function Foo\bar;

                EXPECT,
            ],
            'use-const' => [
                <<<'CODE'
                <?php use const Foo\BAR;
                CODE,
                <<<'EXPECT'
                <?php use const Foo\BAR;

                EXPECT,
            ],

            // Type syntax
            'union-type' => [
                <<<'CODE'
                <?php function f(int|string $x) : void {}
                CODE,
                <<<'EXPECT'
                <?php function f(int|string $x) : void
                {
                }

                EXPECT,
            ],
            'intersection-type' => [
                <<<'CODE'
                <?php function f(Foo&Bar $x) : void {}
                CODE,
                <<<'EXPECT'
                <?php function f(Foo&Bar $x) : void
                {
                }

                EXPECT,
            ],
            'nullable-return' => [
                <<<'CODE'
                <?php function f() : ?int { return null; }
                CODE,
                <<<'EXPECT'
                <?php function f() : ?int
                {
                    return null;
                }

                EXPECT,
            ],

            // Misc expressions
            'paren-expr' => [
                <<<'CODE'
                <?php $x = ($a + $b) * $c;
                CODE,
                <<<'EXPECT'
                <?php $x = ($a + $b) * $c;

                EXPECT,
            ],
            'nested-call' => [
                <<<'CODE'
                <?php $x = foo(bar($a), baz($b));
                CODE,
                <<<'EXPECT'
                <?php $x = foo(bar($a), baz($b));

                EXPECT,
            ],
            'array-assoc' => [
                <<<'CODE'
                <?php $a = ["k" => "v", "k2" => "v2"];
                CODE,
                <<<'EXPECT'
                <?php $a = ["k" => "v", "k2" => "v2"];

                EXPECT,
            ],
            'list-assign' => [
                <<<'CODE'
                <?php [$a, $b] = $arr;
                CODE,
                <<<'EXPECT'
                <?php [$a, $b] = $arr;

                EXPECT,
            ],
            'new-no-args' => [
                <<<'CODE'
                <?php $obj = new Foo();
                CODE,
                <<<'EXPECT'
                <?php $obj = new Foo();

                EXPECT,
            ],
            'new-static' => [
                <<<'CODE'
                <?php $x = new static($a);
                CODE,
                <<<'EXPECT'
                <?php $x = new static($a);

                EXPECT,
            ],
            'new-self' => [
                <<<'CODE'
                <?php $x = new self($a);
                CODE,
                <<<'EXPECT'
                <?php $x = new self($a);

                EXPECT,
            ],
            'new-parent' => [
                <<<'CODE'
                <?php $x = new parent($a);
                CODE,
                <<<'EXPECT'
                <?php $x = new parent($a);

                EXPECT,
            ],
            'clone' => [
                <<<'CODE'
                <?php $b = clone $a;
                CODE,
                <<<'EXPECT'
                <?php $b = clone $a;

                EXPECT,
            ],
            'print' => [
                <<<'CODE'
                <?php print "hello";
                CODE,
                <<<'EXPECT'
                <?php print "hello";

                EXPECT,
            ],
            'global-var' => [
                <<<'CODE'
                <?php function f() { global $x, $y; }
                CODE,
                <<<'EXPECT'
                <?php function f()
                {
                    global $x, $y;
                }

                EXPECT,
            ],
            'static-var' => [
                <<<'CODE'
                <?php function f() { static $x = 1; }
                CODE,
                <<<'EXPECT'
                <?php function f()
                {
                    static $x = 1;
                }

                EXPECT,
            ],
            'const-access' => [
                <<<'CODE'
                <?php Foo::BAR;
                CODE,
                <<<'EXPECT'
                <?php Foo::BAR;

                EXPECT,
            ],
            'decrement' => [
                <<<'CODE'
                <?php $i--; --$j;
                CODE,
                <<<'EXPECT'
                <?php $i --;
                -- $j;

                EXPECT,
            ],
            'string-interp-dollar' => [
                <<<'CODE'
                <?php $s = "${name}";
                CODE,
                <<<'EXPECT'
                <?php $s = "${name}";

                EXPECT,
            ],
            'string-interp-simple' => [
                <<<'CODE'
                <?php $s = "hello $name world";
                CODE,
                <<<'EXPECT'
                <?php $s = "hello $name world";

                EXPECT,
            ],
            'empty-check' => [
                <<<'CODE'
                <?php $x = empty($a);
                CODE,
                <<<'EXPECT'
                <?php $x = empty($a);

                EXPECT,
            ],
            'isset-check' => [
                <<<'CODE'
                <?php $x = isset($a, $b);
                CODE,
                <<<'EXPECT'
                <?php $x = isset($a, $b);

                EXPECT,
            ],
            'unset-call' => [
                <<<'CODE'
                <?php unset($a, $b);
                CODE,
                <<<'EXPECT'
                <?php unset($a, $b);

                EXPECT,
            ],
            'include' => [
                <<<'CODE'
                <?php include "file.php";
                CODE,
                <<<'EXPECT'
                <?php include "file.php";

                EXPECT,
            ],
            'require-once' => [
                <<<'CODE'
                <?php require_once "file.php";
                CODE,
                <<<'EXPECT'
                <?php require_once "file.php";

                EXPECT,
            ],
            'heredoc' => [
                <<<'CODE'
                <?php $s = <<<EOT
                hello
                EOT;
                CODE,
                <<<'EXPECT'
                <?php $s = <<<EOT
                hello
                EOT;

                EXPECT,
            ],
            'nowdoc' => [
                <<<'CODE'
                <?php $s = <<<'EOT'
                hello
                EOT;
                CODE,
                <<<'EXPECT'
                <?php $s = <<<'EOT'
                hello
                EOT;

                EXPECT,
            ],

            // Nullsafe operator
            'nullsafe' => [
                <<<'CODE'
                <?php $x = $obj?->foo();
                CODE,
                <<<'EXPECT'
                <?php $x = $obj?->foo();

                EXPECT,
            ],

            // First-class callable
            'first-class-callable' => [
                <<<'CODE'
                <?php $fn = strlen(...);
                CODE,
                <<<'EXPECT'
                <?php $fn = strlen(...);

                EXPECT,
            ],

            // Multi-catch
            'multi-catch' => [
                <<<'CODE'
                <?php try { f(); } catch (FooException | BarException $e) { log($e); }
                CODE,
                <<<'EXPECT'
                <?php try {
                    f();
                } catch (FooException|BarException $e) {
                    log($e);
                }

                EXPECT,
            ],

            // Anonymous class
            'anon-class' => [
                <<<'CODE'
                <?php $obj = new class ($x) extends Foo {};
                CODE,
                <<<'EXPECT'
                <?php $obj = new class ($x) extends Foo {
                };

                EXPECT,
            ],

            // Anonymous function (no args)
            'anon-func' => [
                <<<'CODE'
                <?php $fn = function () {};
                CODE,
                <<<'EXPECT'
                <?php $fn = function () {
                };

                EXPECT,
            ],

            // Attributes
            'attribute' => [
                <<<'CODE'
                <?php #[Attr] class Foo {}
                CODE,
                <<<'EXPECT'
                <?php #[Attr]
                class Foo
                {
                }

                EXPECT,
            ],
            'attribute-args' => [
                <<<'CODE'
                <?php #[Attr("val")] function f() {}
                CODE,
                <<<'EXPECT'
                <?php #[Attr("val")]
                function f()
                {
                }

                EXPECT,
            ],

            // Readonly
            'readonly-prop' => [
                <<<'CODE'
                <?php class Foo { public readonly int $bar; }
                CODE,
                <<<'EXPECT'
                <?php class Foo
                {
                    public readonly int $bar;
                }

                EXPECT,
            ],
            'readonly-class' => [
                <<<'CODE'
                <?php readonly class Foo {}
                CODE,
                <<<'EXPECT'
                <?php readonly class Foo
                {
                }

                EXPECT,
            ],

            // Constructor promotion
            'constructor-promo' => [
                <<<'CODE'
                <?php class Foo { public function __construct(private int $x, public string $y) {} }
                CODE,
                <<<'EXPECT'
                <?php class Foo
                {
                    public function __construct(private int $x, public string $y)
                    {
                    }
                }

                EXPECT,
            ],

            // Class const visibility
            'const-visibility' => [
                <<<'CODE'
                <?php class Foo { public const BAR = 1; }
                CODE,
                <<<'EXPECT'
                <?php class Foo
                {
                    public const BAR = 1;
                }

                EXPECT,
            ],

            // Static method
            'static-method' => [
                <<<'CODE'
                <?php class Foo { public static function bar() : void {} }
                CODE,
                <<<'EXPECT'
                <?php class Foo
                {
                    public static function bar() : void
                    {
                    }
                }

                EXPECT,
            ],

            // Array push
            'array-push' => [
                <<<'CODE'
                <?php $arr[] = $val;
                CODE,
                <<<'EXPECT'
                <?php $arr[] = $val;

                EXPECT,
            ],

            // Array unpacking
            'array-unpack' => [
                <<<'CODE'
                <?php $a = [...$b, ...$c];
                CODE,
                <<<'EXPECT'
                <?php $a = [...$b, ...$c];

                EXPECT,
            ],

            // Nested array
            'nested-array' => [
                <<<'CODE'
                <?php $a = [[1, 2], [3, 4]];
                CODE,
                <<<'EXPECT'
                <?php $a = [[1, 2], [3, 4]];

                EXPECT,
            ],

            // Chained assignment
            'chained-assign' => [
                <<<'CODE'
                <?php $a = $b = $c = 0;
                CODE,
                <<<'EXPECT'
                <?php $a = $b = $c = 0;

                EXPECT,
            ],

            // Return types
            'self-type' => [
                <<<'CODE'
                <?php class Foo { public function f() : self {} }
                CODE,
                <<<'EXPECT'
                <?php class Foo
                {
                    public function f() : self
                    {
                    }
                }

                EXPECT,
            ],
            'static-type' => [
                <<<'CODE'
                <?php class Foo { public function f() : static {} }
                CODE,
                <<<'EXPECT'
                <?php class Foo
                {
                    public function f() : static
                    {
                    }
                }

                EXPECT,
            ],
            'parent-type' => [
                <<<'CODE'
                <?php class Foo extends Bar { public function f() : parent {} }
                CODE,
                <<<'EXPECT'
                <?php class Foo extends Bar
                {
                    public function f() : parent
                    {
                    }
                }

                EXPECT,
            ],
            'never-type' => [
                <<<'CODE'
                <?php function f() : never { exit; }
                CODE,
                <<<'EXPECT'
                <?php function f() : never
                {
                    exit();
                }

                EXPECT,
            ],
            'mixed-type' => [
                <<<'CODE'
                <?php function f(mixed $x) : mixed {}
                CODE,
                <<<'EXPECT'
                <?php function f(mixed $x) : mixed
                {
                }

                EXPECT,
            ],

            // Goto
            'goto' => [
                <<<'CODE'
                <?php goto end; end: echo "done";
                CODE,
                <<<'EXPECT'
                <?php goto end;
                end:
                echo "done";

                EXPECT,
            ],

            // Break with level
            'break-level' => [
                <<<'CODE'
                <?php while (true) { while (true) { break 2; } }
                CODE,
                <<<'EXPECT'
                <?php while (true) {
                    while (true) {
                        break 2;
                    }
                }

                EXPECT,
            ],

            // Continue
            'continue' => [
                <<<'CODE'
                <?php while (true) { continue; }
                CODE,
                <<<'EXPECT'
                <?php while (true) {
                    continue;
                }

                EXPECT,
            ],

            // Alternative syntax
            'alt-if' => [
                <<<'CODE'
                <?php if ($x): echo $x; endif;
                CODE,
                <<<'EXPECT'
                <?php if ($x):
                    echo $x;
                endif;

                EXPECT,
            ],
            'alt-foreach' => [
                <<<'CODE'
                <?php foreach ($arr as $v): echo $v; endforeach;
                CODE,
                <<<'EXPECT'
                <?php foreach ($arr as $v):
                    echo $v;
                endforeach;

                EXPECT,
            ],

            // String concat assignment
            'string-concat-assign' => [
                <<<'CODE'
                <?php $s .= "more";
                CODE,
                <<<'EXPECT'
                <?php $s .= "more";

                EXPECT,
            ],

            // Double not
            'double-not' => [
                <<<'CODE'
                <?php $x = !!$a;
                CODE,
                <<<'EXPECT'
                <?php $x = ! ! $a;

                EXPECT,
            ],

            // Negative array index
            'negative-index' => [
                <<<'CODE'
                <?php $x = $arr[-1];
                CODE,
                <<<'EXPECT'
                <?php $x = $arr[-1];

                EXPECT,
            ],

            // Nested parentheses
            'nested-paren' => [
                <<<'CODE'
                <?php $x = (($a + $b) * ($c - $d));
                CODE,
                <<<'EXPECT'
                <?php $x = (($a + $b) * ($c - $d));

                EXPECT,
            ],

            // Multiple class constants
            'multiple-const' => [
                <<<'CODE'
                <?php class Foo { const A = 1; const B = 2; }
                CODE,
                <<<'EXPECT'
                <?php class Foo
                {
                    public const A = 1;

                    public const B = 2;
                }

                EXPECT,
            ],

            // Enum with method
            'enum-method' => [
                <<<'CODE'
                <?php enum Color { case Red; public function label() : string { return "red"; } }
                CODE,
                <<<'EXPECT'
                <?php enum Color
                {
                    case Red;

                    public function label() : string
                    {
                        return "red";
                    }
                }

                EXPECT,
            ],

            // Static closure
            'static-closure' => [
                <<<'CODE'
                <?php $fn = static function ($x) { return $x; };
                CODE,
                <<<'EXPECT'
                <?php $fn = static function ($x) {
                    return $x;
                };

                EXPECT,
            ],

            // Multiple named args
            'named-args-multi' => [
                <<<'CODE'
                <?php foo(a: 1, b: 2, c: 3);
                CODE,
                <<<'EXPECT'
                <?php foo(a: 1, b: 2, c: 3);

                EXPECT,
            ],

            // String concat chain
            'concat-chain' => [
                <<<'CODE'
                <?php $s = "a" . "b" . "c" . "d";
                CODE,
                <<<'EXPECT'
                <?php $s = "a" . "b" . "c" . "d";

                EXPECT,
            ],

            // Null coalesce chain
            'coalesce-chain' => [
                <<<'CODE'
                <?php $x = $a ?? $b ?? $c;
                CODE,
                <<<'EXPECT'
                <?php $x = $a ?? $b ?? $c;

                EXPECT,
            ],

            // Instanceof with not
            'instanceof-not' => [
                <<<'CODE'
                <?php $ok = !$obj instanceof Foo;
                CODE,
                <<<'EXPECT'
                <?php $ok = ! $obj instanceof Foo;

                EXPECT,
            ],

            // Interface constant
            'interface-const' => [
                <<<'CODE'
                <?php interface Foo { const BAR = 1; }
                CODE,
                <<<'EXPECT'
                <?php interface Foo
                {
                    public const BAR = 1;
                }

                EXPECT,
            ],

            // Scope resolution ::class
            'scope-res-class' => [
                <<<'CODE'
                <?php $name = Foo::class;
                CODE,
                <<<'EXPECT'
                <?php $name = Foo::class;

                EXPECT,
            ],

            // Dynamic method/property
            'dynamic-method' => [
                <<<'CODE'
                <?php $obj->$method();
                CODE,
                <<<'EXPECT'
                <?php $obj->$method();

                EXPECT,
            ],
            'dynamic-prop' => [
                <<<'CODE'
                <?php $x = $obj->$prop;
                CODE,
                <<<'EXPECT'
                <?php $x = $obj->$prop;

                EXPECT,
            ],

            // String key in brackets
            'string-in-brackets' => [
                <<<'CODE'
                <?php $x = $arr["key"];
                CODE,
                <<<'EXPECT'
                <?php $x = $arr["key"];

                EXPECT,
            ],

            // Numeric literals
            'negative-float' => [
                <<<'CODE'
                <?php $x = -1.5;
                CODE,
                <<<'EXPECT'
                <?php $x = -1.5;

                EXPECT,
            ],
            'scientific' => [
                <<<'CODE'
                <?php $x = 1.5e10;
                CODE,
                <<<'EXPECT'
                <?php $x = 1.5e10;

                EXPECT,
            ],
            'binary-literal' => [
                <<<'CODE'
                <?php $x = 0b1010;
                CODE,
                <<<'EXPECT'
                <?php $x = 0b1010;

                EXPECT,
            ],
            'octal-literal' => [
                <<<'CODE'
                <?php $x = 0o17;
                CODE,
                <<<'EXPECT'
                <?php $x = 0o17;

                EXPECT,
            ],
            'hex-literal' => [
                <<<'CODE'
                <?php $x = 0xFF;
                CODE,
                <<<'EXPECT'
                <?php $x = 0xFF;

                EXPECT,
            ],

            // Match expression
            'match-expr' => [
                <<<'CODE'
                <?php $r = match($x) { 1 => "one", default => "other" };
                CODE,
                <<<'EXPECT'
                <?php $r = match ($x) {
                    1 => "one",
                    default => "other"
                };

                EXPECT,
            ],

            // Exit / die
            'exit-no-arg' => [
                <<<'CODE'
                <?php exit;
                CODE,
                <<<'EXPECT'
                <?php exit();

                EXPECT,
            ],

            // Eval
            'eval' => [
                <<<'CODE'
                <?php eval('$x = 5;');
                CODE,
                <<<'EXPECT'
                <?php eval('$x = 5;');

                EXPECT,
            ],

            // Backtick
            'backtick' => [
                <<<'CODE'
                <?php $out = `ls -la`;
                CODE,
                <<<'EXPECT'
                <?php $out = `ls -la`;

                EXPECT,
            ],

            // Property hooks
            'prop-hook-get' => [
                <<<'CODE'
                <?php class Foo { public string $name { get { return $this->_name; } } }
                CODE,
                <<<'EXPECT'
                <?php class Foo
                {
                    public string $name {
                        get {
                            return $this->_name;
                        }
                    }
                }

                EXPECT,
            ],
            'prop-hook-set' => [
                <<<'CODE'
                <?php class Foo { public string $name { set(string $value) { $this->_name = $value; } } }
                CODE,
                <<<'EXPECT'
                <?php class Foo
                {
                    public string $name {
                        set(string $value) {
                            $this->_name = $value;
                        }
                    }
                }

                EXPECT,
            ],
            'prop-hook-arrow' => [
                <<<'CODE'
                <?php class Foo { public string $name { get => $this->_name; } }
                CODE,
                <<<'EXPECT'
                <?php class Foo
                {
                    public string $name {
                        get => $this->_name;
                    }
                }

                EXPECT,
            ],

            // Abstract property hooks
            'prop-hook-abstract-get' => [
                <<<'CODE'
                <?php interface Foo { public string $name { get; } }
                CODE,
                <<<'EXPECT'
                <?php interface Foo
                {
                    public string $name { get; }
                }

                EXPECT,
            ],
            'prop-hook-abstract-set' => [
                <<<'CODE'
                <?php interface Foo { public string $name { set; } }
                CODE,
                <<<'EXPECT'
                <?php interface Foo
                {
                    public string $name { set; }
                }

                EXPECT,
            ],
            'prop-hook-abstract-get-set' => [
                <<<'CODE'
                <?php interface Foo { public string $name { get; set; } }
                CODE,
                <<<'EXPECT'
                <?php interface Foo
                {
                    public string $name { get; set; }
                }

                EXPECT,
            ],
            'prop-hook-abstract-reorder' => [
                <<<'CODE'
                <?php interface Foo { public string $name { set; get; } }
                CODE,
                <<<'EXPECT'
                <?php interface Foo
                {
                    public string $name { get; set; }
                }

                EXPECT,
            ],

            'prop-hook-abstract-class' => [
                <<<'CODE'
                <?php abstract class Foo { abstract public string $name { set; get; } }
                CODE,
                <<<'EXPECT'
                <?php abstract class Foo
                {
                    abstract public string $name { get; set; }
                }

                EXPECT,
            ],

            // Trait aliasing
            'trait-insteadof' => [
                <<<'CODE'
                <?php class Foo { use A, B { B::hello insteadof A; A::hello as protected helloA; } }
                CODE,
                <<<'EXPECT'
                <?php class Foo
                {
                    use A, B {
                        B::hello insteadof A;
                        A::hello as protected helloA;
                    }
                }

                EXPECT,
            ],

            // Callable / iterable / var
            'callable-type' => [
                <<<'CODE'
                <?php function f(callable $cb) : void {}
                CODE,
                <<<'EXPECT'
                <?php function f(callable $cb) : void
                {
                }

                EXPECT,
            ],
            'iterable-type' => [
                <<<'CODE'
                <?php function f(iterable $items) : void {}
                CODE,
                <<<'EXPECT'
                <?php function f(iterable $items) : void
                {
                }

                EXPECT,
            ],
            'var-keyword' => [
                <<<'CODE'
                <?php class Foo { var $prop; }
                CODE,
                <<<'EXPECT'
                <?php class Foo
                {
                    public $prop;
                }

                EXPECT,
            ],

            // Close tag
            'close-tag' => [
                <<<'CODE'
                <?php echo "test"; ?>
                CODE,
                <<<'EXPECT'
                <?php echo "test";
                ?>

                EXPECT,
            ],

            // Alt syntax (additional)
            'alt-while' => [
                <<<'CODE'
                <?php while ($x > 0): $x--; endwhile;
                CODE,
                <<<'EXPECT'
                <?php while ($x > 0):
                    $x --;
                endwhile;

                EXPECT,
            ],
            'alt-for' => [
                <<<'CODE'
                <?php for ($i = 0; $i < 10; $i++): echo $i; endfor;
                CODE,
                <<<'EXPECT'
                <?php for ($i = 0; $i < 10; $i ++):
                    echo $i;
                endfor;

                EXPECT,
            ],
            'alt-switch' => [
                <<<'CODE'
                <?php switch ($x): case 1: break; default: break; endswitch;
                CODE,
                <<<'EXPECT'
                <?php switch ($x):
                    case 1:
                        break;
                    default:
                        break;
                    endswitch;

                EXPECT,
            ],

            // Closure with ref param
            'closure-ref' => [
                <<<'CODE'
                <?php $fn = function(&$x) { $x++; };
                CODE,
                <<<'EXPECT'
                <?php $fn = function (&$x) {
                    $x ++;
                };

                EXPECT,
            ],

            // Underscore numeric separators
            'underscore-int' => [
                <<<'CODE'
                <?php $n = 1_000_000;
                CODE,
                <<<'EXPECT'
                <?php $n = 1_000_000;

                EXPECT,
            ],
            'underscore-float' => [
                <<<'CODE'
                <?php $n = 1_234.567_89;
                CODE,
                <<<'EXPECT'
                <?php $n = 1_234.567_89;

                EXPECT,
            ],

            // Complex string interpolation
            'interp-array' => [
                <<<'CODE'
                <?php $s = "val: {$arr[0]}";
                CODE,
                <<<'EXPECT'
                <?php $s = "val: {$arr[0]}";

                EXPECT,
            ],
            'interp-prop' => [
                <<<'CODE'
                <?php $s = "name: {$obj->name}";
                CODE,
                <<<'EXPECT'
                <?php $s = "name: {$obj->name}";

                EXPECT,
            ],

            // Multiple attributes
            'multi-attribute' => [
                <<<'CODE'
                <?php #[Attr1] #[Attr2] class Foo {}
                CODE,
                <<<'EXPECT'
                <?php #[Attr1]
                #[Attr2]
                class Foo
                {
                }

                EXPECT,
            ],
            'attr-on-param' => [
                <<<'CODE'
                <?php function f(#[Attr] int $x) {}
                CODE,
                <<<'EXPECT'
                <?php function f(#[Attr] int $x)
                {
                }

                EXPECT,
            ],

            // Constant visibility
            'private-const' => [
                <<<'CODE'
                <?php class Foo { private const X = 1; }
                CODE,
                <<<'EXPECT'
                <?php class Foo
                {
                    private const X = 1;
                }

                EXPECT,
            ],
            'protected-const' => [
                <<<'CODE'
                <?php class Foo { protected const X = 1; }
                CODE,
                <<<'EXPECT'
                <?php class Foo
                {
                    protected const X = 1;
                }

                EXPECT,
            ],

            // Typed properties
            'typed-prop-nullable' => [
                <<<'CODE'
                <?php class Foo { public ?string $name = null; }
                CODE,
                <<<'EXPECT'
                <?php class Foo
                {
                    public ?string $name = null;
                }

                EXPECT,
            ],
            'typed-prop-init' => [
                <<<'CODE'
                <?php class Foo { public array $items = []; }
                CODE,
                <<<'EXPECT'
                <?php class Foo
                {
                    public array $items = [];
                }

                EXPECT,
            ],

            // Final
            'final-class' => [
                <<<'CODE'
                <?php final class Foo {}
                CODE,
                <<<'EXPECT'
                <?php final class Foo
                {
                }

                EXPECT,
            ],
            'final-method' => [
                <<<'CODE'
                <?php class Foo { final public function bar() : void {} }
                CODE,
                <<<'EXPECT'
                <?php class Foo
                {
                    final public function bar() : void
                    {
                    }
                }

                EXPECT,
            ],

            // Multiple interfaces
            'multi-interface' => [
                <<<'CODE'
                <?php class Foo implements Bar, Baz, Qux {}
                CODE,
                <<<'EXPECT'
                <?php class Foo implements Bar, Baz, Qux
                {
                }

                EXPECT,
            ],

            // Instanceof with qualified name
            'instanceof-qualified' => [
                <<<'CODE'
                <?php $x = $obj instanceof \Foo\Bar;
                CODE,
                <<<'EXPECT'
                <?php $x = $obj instanceof \Foo\Bar;

                EXPECT,
            ],

            // Top-level const
            'const-expr' => [
                <<<'CODE'
                <?php const FOO = 42;
                CODE,
                <<<'EXPECT'
                <?php const FOO = 42;

                EXPECT,
            ],

            // Define
            'define' => [
                <<<'CODE'
                <?php define("FOO", 42);
                CODE,
                <<<'EXPECT'
                <?php define("FOO", 42);

                EXPECT,
            ],

            // Throw expression
            'throw-expr' => [
                <<<'CODE'
                <?php $x = $val ?? throw new \Exception("err");
                CODE,
                <<<'EXPECT'
                <?php $x = $val ?? throw new \Exception("err");

                EXPECT,
            ],

            // Nullsafe chain
            'nullsafe-chain' => [
                <<<'CODE'
                <?php $x = $a?->b?->c?->d();
                CODE,
                <<<'EXPECT'
                <?php $x = $a?->b?->c?->d();

                EXPECT,
            ],

            // Array destructuring with keys
            'array-destructure' => [
                <<<'CODE'
                <?php ["a" => $a, "b" => $b] = $data;
                CODE,
                <<<'EXPECT'
                <?php ["a" => $a, "b" => $b] = $data;

                EXPECT,
            ],

            // Power in expression
            'pow-in-expr' => [
                <<<'CODE'
                <?php $x = 2 ** 3 + 1;
                CODE,
                <<<'EXPECT'
                <?php $x = 2 ** 3 + 1;

                EXPECT,
            ],

            // Assignment in condition
            'assign-in-condition' => [
                <<<'CODE'
                <?php if ($x = foo()) { bar(); }
                CODE,
                <<<'EXPECT'
                <?php if ($x = foo()) {
                    bar();
                }

                EXPECT,
            ],

            // Comparison in ternary
            'comparison-chain' => [
                <<<'CODE'
                <?php $min = ($a < $b) ? $a : $b;
                CODE,
                <<<'EXPECT'
                <?php $min = ($a < $b) ? $a : $b;

                EXPECT,
            ],

            // String escapes / single quote
            'string-escape' => [
                <<<'CODE'
                <?php $s = "line1\tline2\n";
                CODE,
                <<<'EXPECT'
                <?php $s = "line1\tline2\n";

                EXPECT,
            ],
            'single-quote' => [
                <<<'CODE'
                <?php $s = 'hello world';
                CODE,
                <<<'EXPECT'
                <?php $s = 'hello world';

                EXPECT,
            ],

            // Boolean/null literals
            'bool-true' => [
                <<<'CODE'
                <?php $x = true;
                CODE,
                <<<'EXPECT'
                <?php $x = true;

                EXPECT,
            ],
            'bool-false' => [
                <<<'CODE'
                <?php $x = false;
                CODE,
                <<<'EXPECT'
                <?php $x = false;

                EXPECT,
            ],
            'null-literal' => [
                <<<'CODE'
                <?php $x = null;
                CODE,
                <<<'EXPECT'
                <?php $x = null;

                EXPECT,
            ],

            // Variadic with type
            'variadic-typed' => [
                <<<'CODE'
                <?php function f(string ...$names) : void {}
                CODE,
                <<<'EXPECT'
                <?php function f(string ...$names) : void
                {
                }

                EXPECT,
            ],

            // Static property via self
            'static-prop-self' => [
                <<<'CODE'
                <?php $x = self::$prop;
                CODE,
                <<<'EXPECT'
                <?php $x = self::$prop;

                EXPECT,
            ],

            // Multiple use
            'use-multi' => [
                <<<'CODE'
                <?php use Foo\Bar, Foo\Baz;
                CODE,
                <<<'EXPECT'
                <?php use Foo\Bar, Foo\Baz;

                EXPECT,
            ],

            // Goto label
            'goto-label' => [
                <<<'CODE'
                <?php start: if ($x) { goto start; }
                CODE,
                <<<'EXPECT'
                <?php start:
                if ($x) {
                    goto start;
                }

                EXPECT,
            ],

            // Ternary with function call
            'ternary-call' => [
                <<<'CODE'
                <?php $x = is_null($a) ? "null" : "not null";
                CODE,
                <<<'EXPECT'
                <?php $x = is_null($a) ? "null" : "not null";

                EXPECT,
            ],

            // Silence with function
            'silence-func' => [
                <<<'CODE'
                <?php $x = @file_get_contents("url");
                CODE,
                <<<'EXPECT'
                <?php $x = @file_get_contents("url");

                EXPECT,
            ],

            // Chained method calls
            'chained-methods' => [
                <<<'CODE'
                <?php $x = $builder->select("*")->from("users")->where("id", 1)->get();
                CODE,
                <<<'EXPECT'
                <?php $x = $builder->select("*")->from("users")->where("id", 1)->get();

                EXPECT,
            ],

            // Enum with interface
            'enum-implements' => [
                <<<'CODE'
                <?php enum Status implements HasLabel { case Active; case Inactive; }
                CODE,
                <<<'EXPECT'
                <?php enum Status implements HasLabel
                {
                    case Active;

                    case Inactive;
                }

                EXPECT,
            ],

            // Readonly constructor promotion
            'readonly-promo' => [
                <<<'CODE'
                <?php class Foo { public function __construct(public readonly int $id) {} }
                CODE,
                <<<'EXPECT'
                <?php class Foo
                {
                    public function __construct(public readonly int $id)
                    {
                    }
                }

                EXPECT,
            ],

            // Intersection return type
            'intersection-return' => [
                <<<'CODE'
                <?php function f() : Foo&Bar {}
                CODE,
                <<<'EXPECT'
                <?php function f() : Foo&Bar
                {
                }

                EXPECT,
            ],

            // Nested ternary in parens
            'ternary-nested' => [
                <<<'CODE'
                <?php $x = $a ? ($b ? 1 : 2) : 3;
                CODE,
                <<<'EXPECT'
                <?php $x = $a ? ($b ? 1 : 2) : 3;

                EXPECT,
            ],

            // Ternary in function args
            'ternary-in-args' => [
                <<<'CODE'
                <?php foo($a ? $b : $c, $d);
                CODE,
                <<<'EXPECT'
                <?php foo($a ? $b : $c, $d);

                EXPECT,
            ],

            // Ternary in array index
            'ternary-in-array' => [
                <<<'CODE'
                <?php $arr[$a ? $b : $c];
                CODE,
                <<<'EXPECT'
                <?php $arr[$a ? $b : $c];

                EXPECT,
            ],

            // Elvis in parens
            'elvis-in-paren' => [
                <<<'CODE'
                <?php $x = ($a ?: $b);
                CODE,
                <<<'EXPECT'
                <?php $x = ($a ?: $b);

                EXPECT,
            ],

            // Ternary in match arm
            'ternary-in-match' => [
                <<<'CODE'
                <?php $r = match($x) { 1 => $a ? $b : $c, 2 => $d };
                CODE,
                <<<'EXPECT'
                <?php $r = match ($x) {
                    1 => $a ? $b : $c,
                    2 => $d
                };

                EXPECT,
            ],

            // Full class example
            'full-class' => [
                <<<'CODE'
                <?php
                declare(strict_types=1);
                namespace App\Domain;
                use App\Contract\Loggable; use App\Base\AbstractEntity;
                /**
                 * A full-featured example class.
                 */
                #[Entity("users")] #[Cacheable(ttl: 3600)] class User extends AbstractEntity implements Loggable, \Stringable { use SimpleTrait; use TraitA, TraitB { TraitB::method insteadof TraitA; TraitA::method as protected traitAMethod; } public const STATUS_ACTIVE = "active"; protected const STATUS_INACTIVE = "inactive"; private static int $instanceCount = 0; public readonly string $createdAt; public string $displayName { get { return $this->firstName . " " . $this->lastName; } } public string $email { set(string $value) { $this->email = strtolower($value); } } public int $age { get => $this->age; set => $this->age = $value; } public function __construct(private string $firstName, protected string $lastName, string $email) { parent::__construct(); self::$instanceCount++; $this->createdAt = date("c"); $this->email = $email; } public function __destruct() { self::$instanceCount--; } public function getLog() : string { return sprintf("[%s] %s", $this->createdAt, $this->displayName); } public static function getInstanceCount() : int { return self::$instanceCount; } public function __toString() : string { return $this->displayName; } }

                CODE,
                <<<'EXPECT'
                <?php
                declare(strict_types=1);

                namespace App\Domain;

                use App\Contract\Loggable;
                use App\Base\AbstractEntity;

                /**
                 * A full-featured example class.
                 */
                #[Entity("users")]
                #[Cacheable(ttl: 3600)]
                class User extends AbstractEntity implements Loggable, \Stringable
                {
                    use SimpleTrait;

                    use TraitA, TraitB {
                        TraitB::method insteadof TraitA;
                        TraitA::method as protected traitAMethod;
                    }

                    public const STATUS_ACTIVE = "active";

                    protected const STATUS_INACTIVE = "inactive";

                    private static int $instanceCount = 0;

                    public readonly string $createdAt;

                    public string $displayName {
                        get {
                            return $this->firstName . " " . $this->lastName;
                        }
                    }

                    public string $email {
                        set(string $value) {
                            $this->email = strtolower($value);
                        }
                    }

                    public int $age {
                        get => $this->age;
                        set => $this->age = $value;
                    }

                    public function __construct(
                        private string $firstName,
                        protected string $lastName,
                        string $email,
                    ) {
                        parent::__construct();
                        self::$instanceCount ++;
                        $this->createdAt = date("c");
                        $this->email = $email;
                    }

                    public function __destruct()
                    {
                        self::$instanceCount --;
                    }

                    public function getLog() : string
                    {
                        return sprintf("[%s] %s", $this->createdAt, $this->displayName);
                    }

                    public static function getInstanceCount() : int
                    {
                        return self::$instanceCount;
                    }

                    public function __toString() : string
                    {
                        return $this->displayName;
                    }
                }

                EXPECT,
            ],

            // Full interface example
            'full-interface' => [
                <<<'CODE'
                <?php
                namespace App\Contract;
                /** * Loggable contract. */
                interface Loggable extends \Stringable { const LOG_LEVEL_INFO = "info"; const LOG_LEVEL_ERROR = "error"; public function getLog() : string; public function getLogLevel() : string; }
                CODE,
                <<<'EXPECT'
                <?php
                namespace App\Contract;

                /** * Loggable contract. */
                interface Loggable extends \Stringable
                {
                    public const LOG_LEVEL_INFO = "info";

                    public const LOG_LEVEL_ERROR = "error";

                    public function getLog() : string;

                    public function getLogLevel() : string;
                }

                EXPECT,
            ],

            // Full abstract class example
            'full-abstract-class' => [
                <<<'CODE'
                <?php
                namespace App\Base;
                use App\Contract\Loggable;
                /** * Base entity with common functionality. */
                #[MappedSuperclass]
                abstract class AbstractEntity implements Loggable { protected string $id; abstract public function getLog() : string; abstract protected function validate() : bool; public function getId() : string { return $this->id; } public function getLogLevel() : string { return "info"; } public function __toString() : string { return static::class . ":" . $this->id; } }
                CODE,
                <<<'EXPECT'
                <?php
                namespace App\Base;

                use App\Contract\Loggable;

                /** * Base entity with common functionality. */
                #[MappedSuperclass]
                abstract class AbstractEntity implements Loggable
                {
                    protected string $id;

                    abstract public function getLog() : string;

                    abstract protected function validate() : bool;

                    public function getId() : string
                    {
                        return $this->id;
                    }

                    public function getLogLevel() : string
                    {
                        return "info";
                    }

                    public function __toString() : string
                    {
                        return static::class . ":" . $this->id;
                    }
                }

                EXPECT,
            ],

            // Full backed enum example
            'full-backed-enum' => [
                <<<'CODE'
                <?php
                namespace App\Domain;
                /** * HTTP status codes. */
                enum HttpStatus : int implements \Stringable { case Ok = 200; case NotFound = 404; case InternalError = 500; const DEFAULT = self::Ok; public function label() : string { return match ($this) { self::Ok => "OK", self::NotFound => "Not Found", self::InternalError => "Internal Server Error", }; } public function isError() : bool { return $this->value >= 400; } public function __toString() : string { return $this->label(); } }
                CODE,
                <<<'EXPECT'
                <?php
                namespace App\Domain;

                /** * HTTP status codes. */
                enum HttpStatus : int implements \Stringable
                {
                    case Ok = 200;

                    case NotFound = 404;

                    case InternalError = 500;

                    public const DEFAULT = self::Ok;

                    public function label() : string
                    {
                        return match ($this) {
                            self::Ok => "OK",
                            self::NotFound => "Not Found",
                            self::InternalError => "Internal Server Error",
                        };
                    }

                    public function isError() : bool
                    {
                        return $this->value >= 400;
                    }

                    public function __toString() : string
                    {
                        return $this->label();
                    }
                }

                EXPECT,
            ],

            // Full non-backed enum example
            'full-enum' => [
                <<<'CODE'
                <?php
                namespace App\Domain;
                /** * Card suits. */
                enum Suit { case Hearts; case Diamonds; case Clubs; case Spades; const RED = [self::Hearts, self::Diamonds]; public function color() : string { return match ($this) { self::Hearts, self::Diamonds => "red", self::Clubs, self::Spades => "black", }; } public function isRed() : bool { return $this->color() === "red"; } }
                CODE,
                <<<'EXPECT'
                <?php
                namespace App\Domain;

                /** * Card suits. */
                enum Suit
                {
                    case Hearts;

                    case Diamonds;

                    case Clubs;

                    case Spades;

                    public const RED = [self::Hearts, self::Diamonds];

                    public function color() : string
                    {
                        return match ($this) {
                            self::Hearts, self::Diamonds => "red",
                            self::Clubs, self::Spades => "black",
                        };
                    }

                    public function isRed() : bool
                    {
                        return $this->color() === "red";
                    }
                }

                EXPECT,
            ],

            // Line splitting: long function call (P1)
            'split-function-args' => [
                <<<'CODE'
                <?php
                someLongFunctionName($veryLongArgumentNameOne, $veryLongArgumentNameTwo, $veryLongArgumentNameThree);
                CODE,
                <<<'EXPECT'
                <?php

                someLongFunctionName(
                    $veryLongArgumentNameOne,
                    $veryLongArgumentNameTwo,
                    $veryLongArgumentNameThree,
                );

                EXPECT,
            ],

            // Line splitting: long method chain (P2)
            'split-method-chain' => [
                <<<'CODE'
                <?php
                $result = $object->firstLongMethod()->secondLongMethod()->thirdLongMethod()->fourthLongMethod();
                CODE,
                <<<'EXPECT'
                <?php

                $result = $object->firstLongMethod()
                    ->secondLongMethod()
                    ->thirdLongMethod()
                    ->fourthLongMethod();

                EXPECT,
            ],

            // Line splitting: long boolean expression (P3)
            'split-boolean-expression' => [
                <<<'CODE'
                <?php
                $result = $veryLongConditionAlphaName && $veryLongConditionBetaName && $veryLongConditionGammaName;
                CODE,
                <<<'EXPECT'
                <?php

                $result = $veryLongConditionAlphaName
                    && $veryLongConditionBetaName
                    && $veryLongConditionGammaName;

                EXPECT,
            ],

        ];
    }

    private Styler $styler;

    protected function setUp() : void
    {
        $this->styler = new Styler(
            new TestFormat(rules: [
                ExpandGroupedImports::class,
                ConvertVarToPublic::class,
                InsertPublicVisibility::class,
                NormalizeModifierOrder::class,
                ConvertToShortArraySyntax::class,
                ConvertToShortListSyntax::class,
                MergeParenBrace::class,
                NormalizeTrailingCommas::class,
                RemoveTrailingBlankLines::class,
            ]),
        );
    }

    #[DataProvider('provide')]
    public function test(string $code, string $expect) : void
    {
        $actual = ($this->styler)($code);
        $this->assertSame($expect, $actual);
    }
}
