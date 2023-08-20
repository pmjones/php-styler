<?php
declare(strict_types=1);

namespace PhpStyler;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class Visitor extends NodeVisitorAbstract
{
    protected $fluent_idx = 0;

    protected $fluent_rev = [];

    public function enterNode(Node $node) : void
    {
        if (
            $node instanceof Node\Expr\MethodCall
            || $node instanceof Node\Expr\NullsafeMethodCall
            || $node instanceof Node\Expr\NullsafePropertyFetch
            || $node instanceof Node\Expr\PropertyFetch
        ) {
            $this->fluent_rev[$this->fluent_idx] ??= 0;
            $this->fluent_rev[$this->fluent_idx] ++;
            $node->setAttribute('fluent_idx', $this->fluent_idx);
            $node->setAttribute('fluent_num', null);
            $node->setAttribute('fluent_end', null);
            $node->setAttribute('fluent_rev', $this->fluent_rev[$this->fluent_idx]);
        } else {
            $this->fluent_idx ++;
        }
    }

    public function leaveNode(Node $node) : void
    {
        if (
            $node instanceof Node\Expr\MethodCall
            || $node instanceof Node\Expr\NullsafeMethodCall
            || $node instanceof Node\Expr\NullsafePropertyFetch
            || $node instanceof Node\Expr\PropertyFetch
        ) {
            // visitor encounters the nodes in reverse order, so reverse
            // the fluent_rev to get a count up instead of a count down
            $fluent_idx = $node->getAttribute('fluent_idx');
            $fluent_end = $this->fluent_rev[$fluent_idx];
            $fluent_rev = $node->getAttribute('fluent_rev');
            $node->setAttribute('fluent_end', $fluent_end);
            $node->setAttribute('fluent_num', $fluent_end - $fluent_rev + 1);
        }
    }
}
