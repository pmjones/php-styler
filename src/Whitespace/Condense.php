<?php
declare(strict_types=1);

namespace PhpStyler\Whitespace;

use PhpStyler\Whitespace;

class Condense extends Whitespace
{
    /**
     * @param callable $when
     */
    public function __construct(
        public readonly mixed $when,
        public readonly string $append = '',
    ) {
    }
}
