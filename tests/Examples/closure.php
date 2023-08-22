<?php
$foo = function ($bar) use ($baz) : mixed {
    $i ++;
};
$dib = function () {
    $i ++;
};
$veryLongVariableName = function (
    $veryLongVar1,
    $veryLongVar2,
) use (
    $veryLongVar3,
    $veryLongVar4,
) {
    $i ++;
};
foo(
    function () {
        /* code */
    },
);
foo(
    $bar,
    function () {
        /* code */
    },
);
foo(
    function () {
        /* code */
    },
    $baz,
);
foo(
    $bar,
    function () {
        /* code */
    },
    $baz,
);
$result = $this
    ->veryLongProperty
    ->veryLongMethod(
        $veryLongVariableName,
        new VeryLongClassName(
            static function () : void {
                throw VeryLongException::create();
            },
            $this->veryLongVariableName,
        ),
    );
$shortVar = array_reduce(
    $foo,
    function ($addr, $addrs) {
        if (
            'REMOTE_ADDR' !== $addr
        ) {
            $addrs[] = $addr;
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $addrs[] = $_SERVER['REMOTE_ADDR'];
        }

        return $addr;
    },
    [],
);

class foo
{
    public function bar(baz $e)
    {
        $result = $this
            ->func
            ->proc(
                $psr7Request,
                new VeryLongClassName(
                    static function () : void {
                        throw ReachedFinalHandlerException::create();
                    },
                    $this->veryLongProperty,
                ),
            );
        $e->setResult($result);
        return $result;
    }
}
