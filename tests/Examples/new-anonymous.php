<?php
$foo = new class (10) extends SomeClass implements SomeInterface {
    use SomeTrait;

    private int $num;

    public function __construct(int $num)
    {
        $this->num = $num;
    }
};

$bar = new class (
    $firstArgumentValue,
    $secondArgumentValue,
    $thirdArgumentValue,
) extends SomeVeryLongParentClassName {
    public int $num = 1;
};

$baz = new class (
    ['hello.php', 'World'],
    $stdout,
    $stderr,
) extends ConsoleFrontController {
    public int $num = 1;
};

$qux = new class (
    argv: ['hello.php'],
    stdout: $stdout,
    stderr: $stderr,
) extends ConsoleFrontController {
    public int $num = 1;
};
