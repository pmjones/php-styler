<?php
declare(strict_types=1);

namespace PhpStyler\Token;

abstract class ACommaListOpener extends AToken
{
    public int $argCount = 0;

    /**
     * @return class-string<ASplittableComma&AToken>
     */
    abstract public function commaClass() : string;
}
