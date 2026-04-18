<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;

abstract class ASplittableComma extends AToken implements ASplittable
{
    /** @var class-string<TSplit> */
    protected const SPLIT_CLASS = TSplitComma::class;

    public function splitAfter(Parser $parser) : ?TSplit
    {
        /** @var TSplit */
        return new (static::SPLIT_CLASS)(AToken::SYNTHETIC, '');
    }
}
