<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Format\DeclarationFormat;
use PhpStyler\Styler;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class THeredocStartAsNowdocTest extends TestCase
{
    /** @return array<string, array{0: string, 1: string}> */
    public static function provide() : array
    {
        return [
            'no-interpolation' => [
                <<<'CODE'
                <?php
                $x = <<<EOT
                hello world
                EOT;
                CODE,
                <<<'EXPECT'
                <?php
                $x = <<<'EOT'
                hello world
                EOT;

                EXPECT,
            ],
            'with-variable-interpolation' => [
                <<<'CODE'
                <?php
                $x = <<<EOT
                hello $name
                EOT;
                CODE,
                <<<'EXPECT'
                <?php
                $x = <<<EOT
                hello {$name}
                EOT;

                EXPECT,
            ],
            'with-curly-interpolation' => [
                <<<'CODE'
                <?php
                $x = <<<EOT
                hello {$name}
                EOT;
                CODE,
                <<<'EXPECT'
                <?php
                $x = <<<EOT
                hello {$name}
                EOT;

                EXPECT,
            ],
            'already-nowdoc' => [
                <<<'CODE'
                <?php
                $x = <<<'EOT'
                hello world
                EOT;
                CODE,
                <<<'EXPECT'
                <?php
                $x = <<<'EOT'
                hello world
                EOT;

                EXPECT,
            ],
        ];
    }

    #[DataProvider('provide')]
    public function test(string $code, string $expect) : void
    {
        $styler = new Styler(
            new DeclarationFormat(parseAs: [
                THeredocStart::class => THeredocStartAsNowdoc::class,
            ]),
        );

        $actual = $styler($code);
        $this->assertSame($expect, $actual);
    }
}
