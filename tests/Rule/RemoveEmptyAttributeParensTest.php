<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Format\DeclarationFormat;
use PhpStyler\Styler;
use PHPUnit\Framework\TestCase;

class RemoveEmptyAttributeParensTest extends TestCase
{
    /**
     * @dataProvider provide
     */
    public function test(string $code, string $expect) : void
    {
        $styler = new Styler(
            new DeclarationFormat(rules: [
                RemoveEmptyAttributeParens::class,
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
            'remove-empty-attribute-parens' => [
                <<<'CODE'
                <?php
                #[Override()]
                function foo() {}
                CODE,
                <<<'EXPECT'
                <?php
                #[Override]
                function foo()
                {
                }

                EXPECT,
            ],
            'keep-attribute-parens-with-args' => [
                <<<'CODE'
                <?php
                #[Route('/path')]
                function foo() {}
                CODE,
                <<<'EXPECT'
                <?php
                #[Route('/path')]
                function foo()
                {
                }

                EXPECT,
            ],
            'function-call-parens-unchanged' => [
                <<<'CODE'
                <?php
                foo();
                CODE,
                <<<'EXPECT'
                <?php
                foo();

                EXPECT,
            ],
        ];
    }
}
