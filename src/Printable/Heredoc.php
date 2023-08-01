<?php
declare(strict_types=1);

namespace PhpStyler\Printable;

class Heredoc extends Printable
{
    public function __construct(
        public string $label,
    ) {
    }
}
