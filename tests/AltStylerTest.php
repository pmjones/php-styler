<?php
declare(strict_types=1);

namespace PhpStyler;

class AltStylerTest extends TestCase
{
    public function test() : void
    {
        $this->service = new Service(new AltStyler());

        $code = <<<'CODE'
        <?php
        function foo()
        {
            // logic
        }

        function bar(): VeryVeryVeryLongHint
        {
            // logic
        }

        function thisVeryVeryLongFunctionName(
            VeryVeryVeryLongHint $veryVeryVeryLongArg1,
            VeryVeryVeryLongHint $veryVeryVeryLongArg2,
            VeryVeryVeryLongHint $veryVeryVeryLongArg3,
            VeryVeryVeryLongHint $veryVeryVeryLongArg4,
        ) {
            // logic
        }

        function thatVeryVeryVeryLongFunctionName(
            VeryVeryVeryLongHint $veryVeryVeryLongArg1,
            VeryVeryVeryLongHint $veryVeryVeryLongArg2,
            VeryVeryVeryLongHint $veryVeryVeryLongArg3,
            VeryVeryVeryLongHint $veryVeryVeryLongArg4,
        ): VeryVeryVeryLongHint {
            // logic
        }

        CODE;

        $this->assertPrint($code, $code);
    }
}
