<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TImplementsComma extends ASplittableComma
{
    protected const SPLIT_CLASS = TSplitListComma::class;
}
