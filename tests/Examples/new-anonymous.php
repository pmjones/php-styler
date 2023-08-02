<?php
$foo = new class(10) extends SomeClass implements SomeInterface {
    use SomeTrait;

    private int $num;

    public function __construct(int $num)
    {
        $this->num = $num;
    }
};
