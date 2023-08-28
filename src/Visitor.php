<?php
declare(strict_types=1);

namespace PhpStyler;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\NodeVisitorAbstract;

class Visitor extends NodeVisitorAbstract
{
    protected int $fluent_idx = 0;

    /**
     * @var int[]
     */
    protected array $fluent_rev = [];

    public function enterNode(Node $node) : null|int|Node
    {
        // fluent call?
        if (
            $node instanceof Expr\MethodCall
            || $node instanceof Expr\New_
            || $node instanceof Expr\NullsafeMethodCall
            || $node instanceof Expr\NullsafePropertyFetch
            || $node instanceof Expr\PropertyFetch
            || $node instanceof Expr\StaticCall
            || $node instanceof Expr\StaticPropertyFetch
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

        // closure or new in argument?
        if (
            $node instanceof Expr\FuncCall
            || $node instanceof Expr\MethodCall
            || $node instanceof Expr\New_
            || $node instanceof Expr\NullsafeMethodCall
            || $node instanceof Expr\NullsafePropertyFetch
            || $node instanceof Expr\StaticCall
        ) {
            $node->setAttribute('has_expansive_arg', false);
            $args = $node->args ?? [];

            // note the possibly-expansive arg only if
            // there are multiple args in the list.
            if (count($args) > 1) {
                foreach ($args as $arg) {
                    if (
                        isset($arg->value) && (
                            $arg->value instanceof Expr\Closure && $arg->value->stmts
                            || $arg->value instanceof Expr\ArrowFunction
                            || $arg->value instanceof Expr\New_ && $arg->value->args
                            || $arg->value instanceof Expr\Array_ && $arg->value->items
                        )
                    ) {
                        $node->setAttribute('has_expansive_arg', true);
                    }
                }
            }
        }

        return null;
    }

    /**
     * @return null|int|Node|Node[]
     */
    public function leaveNode(Node $node) : null|int|Node|array
    {
        // retain fluency info
        if (
            $node instanceof Expr\MethodCall
            || $node instanceof Expr\NullsafeMethodCall
            || $node instanceof Expr\NullsafePropertyFetch
            || $node instanceof Expr\PropertyFetch
            || $node instanceof Expr\StaticCall
            || $node instanceof Expr\StaticPropertyFetch
        ) {
            // visitor encounters the nodes in reverse order, so reverse
            // the fluent_rev to get a count up instead of a count down
            $fluent_idx = $node->getAttribute('fluent_idx');
            $fluent_end = $this->fluent_rev[$fluent_idx];
            $fluent_rev = $node->getAttribute('fluent_rev');
            $node->setAttribute('fluent_end', $fluent_end);
            $node->setAttribute('fluent_num', $fluent_end - $fluent_rev + 1);
        }

        return null;
    }
}
