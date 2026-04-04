<?php
declare(strict_types=1);

namespace PhpStyler\Token;

interface ACommaListOpener
{
    /**
     * @return class-string<ASplittableComma&AToken>
     */
    public function commaClass() : string;
}
