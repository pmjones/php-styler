<?php
declare(strict_types=1);

namespace PhpStyler\Rule\TokenRule;

use PhpStyler\Format\DeclarationFormat;
use PhpStyler\Rule\LineRule\RemoveTrailingBlankLines;
use PhpStyler\Styler;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ConvertToYodaConditionsTest extends TestCase
{
    /** @return array<string, array{0: string, 1: string}> */
    public static function provide() : array
    {
        return [
            'basic-identical' => [
                <<<'CODE'
                <?php
                if ($var === null) {
                }
                CODE,
                <<<'EXPECT'
                <?php
                if (null === $var) {
                }

                EXPECT,
            ],
            'basic-not-identical' => [
                <<<'CODE'
                <?php
                if ($var !== false) {
                }
                CODE,
                <<<'EXPECT'
                <?php
                if (false !== $var) {
                }

                EXPECT,
            ],
            'basic-equal' => [
                <<<'CODE'
                <?php
                if ($var == 'foo') {
                }
                CODE,
                <<<'EXPECT'
                <?php
                if ('foo' == $var) {
                }

                EXPECT,
            ],
            'basic-not-equal' => [
                <<<'CODE'
                <?php
                if ($var != 0) {
                }
                CODE,
                <<<'EXPECT'
                <?php
                if (0 != $var) {
                }

                EXPECT,
            ],
            'already-yoda' => [
                <<<'CODE'
                <?php
                if (null === $var) {
                }
                CODE,
                <<<'EXPECT'
                <?php
                if (null === $var) {
                }

                EXPECT,
            ],
            'both-variables' => [
                <<<'CODE'
                <?php
                if ($a === $b) {
                }
                CODE,
                <<<'EXPECT'
                <?php
                if ($a === $b) {
                }

                EXPECT,
            ],
            'complex-left-expression' => [
                <<<'CODE'
                <?php
                if ($obj->prop === null) {
                }
                CODE,
                <<<'EXPECT'
                <?php
                if ($obj->prop === null) {
                }

                EXPECT,
            ],
            'negated-variable' => [
                <<<'CODE'
                <?php
                if (!$var === null) {
                }
                CODE,
                <<<'EXPECT'
                <?php
                if (! $var === null) {
                }

                EXPECT,
            ],
            'integer-literal' => [
                <<<'CODE'
                <?php
                if ($var === 42) {
                }
                CODE,
                <<<'EXPECT'
                <?php
                if (42 === $var) {
                }

                EXPECT,
            ],
            'float-literal' => [
                <<<'CODE'
                <?php
                if ($var === 1.5) {
                }
                CODE,
                <<<'EXPECT'
                <?php
                if (1.5 === $var) {
                }

                EXPECT,
            ],
            'string-literal' => [
                <<<'CODE'
                <?php
                if ($var === "hello") {
                }
                CODE,
                <<<'EXPECT'
                <?php
                if ("hello" === $var) {
                }

                EXPECT,
            ],
            'true-literal' => [
                <<<'CODE'
                <?php
                if ($var === true) {
                }
                CODE,
                <<<'EXPECT'
                <?php
                if (true === $var) {
                }

                EXPECT,
            ],
        ];
    }

    #[DataProvider('provide')]
    public function test(string $code, string $expect) : void
    {
        $styler = new Styler(
            new DeclarationFormat(rules: [
                ConvertToYodaConditions::class,
                RemoveTrailingBlankLines::class,
            ]),
        );

        $actual = $styler($code);
        $this->assertSame($expect, $actual);
    }
}
