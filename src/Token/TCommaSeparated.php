<?php
declare(strict_types=1);

namespace PhpStyler\Token;

interface TCommaSeparated
{
    /** @return class-string<TSplittableComma&T> */
    public function commaClass() : string;
}
