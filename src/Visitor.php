<?php
declare(strict_types=1);

namespace PhpStyler;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\NodeVisitorAbstract;

class Visitor extends NodeVisitorAbstract
{
    protected int $fluentIdx = 0;

    /**
     * @var int[]
     */
    protected array $fluentRev = [];

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
            $this->fluentRev[$this->fluentIdx] ??= 0;
            $this->fluentRev[$this->fluentIdx] ++;
            $node->setAttribute('fluentIdx', $this->fluentIdx);
            $node->setAttribute('fluentNum', null);
            $node->setAttribute('fluentEnd', null);
            $node->setAttribute('fluentRev', $this->fluentRev[$this->fluentIdx]);
        } else {
            $this->fluentIdx ++;
        }

        // closure, new, or array in arguments? expansive.
        if (
            $node instanceof Expr\FuncCall
            || $node instanceof Expr\MethodCall
            || $node instanceof Expr\New_
            || $node instanceof Expr\NullsafeMethodCall
            || $node instanceof Expr\NullsafePropertyFetch
            || $node instanceof Expr\StaticCall
        ) {
            $args = $node->args ?? [];

            // expansive only if multiple args.
            if (count($args) > 1) {
                foreach ($args as $arg) {
                    if (! isset($arg->value)) {
                        continue;
                    }

                    if (
                        $arg->value instanceof Expr\Closure && $arg->value->stmts
                        || $arg->value instanceof Expr\ArrowFunction
                        || $arg->value instanceof Expr\New_ && $arg->value->args
                        || $arg->value instanceof Expr\Array_ && $arg->value->items
                        || $arg->value instanceof Expr\Ternary
                    ) {
                        $node->setAttribute('expansive', true);
                        break;
                    }
                }
            }
        }

        // attributes or comments in params? expansive.
        foreach ($node->params ?? [] as $param) {
            if ($param->getComments()) {
                $node->setAttribute('expansive', true);
            }

            if ($param->attrGroups ?? []) {
                $node->setAttribute('expansive', true);
            }
        }

        // comments in array? expansive.
        if ($node instanceof Expr\Array_) {
            foreach ($node->items as $item) {
                if ($item?->getComments() ?? false) {
                    $node->setAttribute('expansive', true);
                    break;
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
            // the fluentRev to get a count up instead of a count down
            $fluentIdx = $node->getAttribute('fluentIdx');
            $fluentEnd = $this->fluentRev[$fluentIdx];
            $fluentRev = $node->getAttribute('fluentRev');
            $node->setAttribute('fluentEnd', $fluentEnd);
            $node->setAttribute('fluentNum', $fluentEnd - $fluentRev + 1);
        }

        return null;
    }
}
