<?php
declare(strict_types=1);

namespace PhpStyler;

class ConfigTest extends TestCase
{
    public function test() : void
    {
        $actual = new Config(new Styler(), new Files(), null);
        $this->assertInstanceof(Config::class, $actual);
    }
}
