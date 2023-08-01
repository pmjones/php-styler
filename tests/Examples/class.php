<?php
abstract class Bar extends Baz implements Dib, Zim, Gir
{
    public const FOOCON = 'FOOCON';

    private readonly ?string $bar;

    protected int $count = 0;

    public function __construct(public bool $baz = false)
    {
    }

    final private static function doom()
    {
    }

    abstract protected function irk();
}
