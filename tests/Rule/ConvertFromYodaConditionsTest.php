<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Format\DeclarationFormat;
use PhpStyler\Styler;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ConvertFromYodaConditionsTest extends TestCase
{
    #[DataProvider('provide')]
    public function test(string $code, string $expect) : void
    {
        $styler = new Styler(
            new DeclarationFormat(rules: [
                ConvertFromYodaConditions::class,
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
            'null-identical' => [
                <<<'CODE'
                <?php
                if (null === $var) {
                }
                CODE,
                <<<'EXPECT'
                <?php
                if ($var === null) {
                }

                EXPECT,
            ],
            'false-not-identical' => [
                <<<'CODE'
                <?php
                if (false !== $var) {
                }
                CODE,
                <<<'EXPECT'
                <?php
                if ($var !== false) {
                }

                EXPECT,
            ],
            'string-equal' => [
                <<<'CODE'
                <?php
                if ('foo' == $var) {
                }
                CODE,
                <<<'EXPECT'
                <?php
                if ($var == 'foo') {
                }

                EXPECT,
            ],
            'integer-not-equal' => [
                <<<'CODE'
                <?php
                if (0 != $var) {
                }
                CODE,
                <<<'EXPECT'
                <?php
                if ($var != 0) {
                }

                EXPECT,
            ],
            'already-non-yoda' => [
                <<<'CODE'
                <?php
                if ($var === null) {
                }
                CODE,
                <<<'EXPECT'
                <?php
                if ($var === null) {
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
            'integer-literal' => [
                <<<'CODE'
                <?php
                if (42 === $var) {
                }
                CODE,
                <<<'EXPECT'
                <?php
                if ($var === 42) {
                }

                EXPECT,
            ],
            'unary-prefix-preserved' => [
                <<<'CODE'
                <?php
                if (-1 === $var) {
                }
                CODE,
                <<<'EXPECT'
                <?php
                if ($var === -1) {
                }

                EXPECT,
            ],
        ];
    }
}
