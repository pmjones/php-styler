<?php
declare(strict_types=1);

namespace PhpStyler\Space;

use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;
use PhpStyler\Printable as P;

class Split
{
    /**
     * @var mixed[]
     */
    public readonly array $args;

    public function __construct(
        public readonly int $level,
        public readonly string $rule,
        public readonly ?string $type = null,
        mixed ...$args,
    ) {
        $this->args = $args;
    }
}
