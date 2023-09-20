<?php
declare(strict_types=1);

namespace PhpStyler;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\NodeVisitorAbstract;

class Visitor extends NodeVisitorAbstract
{
    protected int $fluentIdx = 0;

    protected int $expansive = 0;

    /**
     * @var int[]
     */
    protected array $fluentRev = [];

    public function enterNode(Node $node) : null|int|Node
    {
        $this->enterNodeFluency($node);
        $this->enterNodeExpansive($node);
        return null;
    }

    protected function enterNodeFluency(Node $node) : void
    {
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
    }

    protected function enterNodeExpansive(Node $node) : ?bool
    {
        return $this->enterNodeExpansiveCall($node)
            ?? $this->enterNodeExpansiveParams($node)
            ?? $this->enterNodeExpansiveArray($node)
            ?? null;
    }

    protected function enterNodeExpansiveCall(Node $node) : ?bool
    {
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
                    if ($arg->getComments()) {
                        $node->setAttribute('expansive', true);
                        return true;
                    }

                    if (! isset($arg->value)) {
                        continue;
                    }

                    if (
                        $arg->value instanceof Expr\ArrowFunction
                        || $arg->value instanceof Expr\Closure && $arg->value->stmts
                    ) {
                        $node->setAttribute('expansive', true);
                        return true;
                    }
                }
            }
        }

        return null;
    }

    protected function enterNodeExpansiveParams(Node $node) : ?bool
    {
        foreach ($node->params ?? [] as $param) {
            if ($param?->getComments()) {
                $node->setAttribute('expansive', true);
                return true;
            }

            if ($param->attrGroups ?? []) {
                $node->setAttribute('expansive', true);
                return true;
            }
        }

        return null;
    }

    protected function enterNodeExpansiveArray(Node $node) : ?bool
    {
        if (! $node instanceof Expr\Array_) {
            return null;
        }

        foreach ($node->items as $item) {
            if ($item?->getComments() ?? false) {
                $node->setAttribute('expansive', true);
                return true;
            }

            if (! isset($item->value)) {
                continue;
            }

            if (
                $item->value instanceof Expr\ArrowFunction
                || $item->value instanceof Expr\Closure && $item->value->stmts
            ) {
                $node->setAttribute('expansive', true);
                return true;
            }
        }

        return null;
    }

    /**
     * @return null|int|Node|Node[]
     */
    public function leaveNode(Node $node) : null|int|Node|array
    {
        $this->leaveNodeFluency($node);
        return null;
    }

    protected function leaveNodeFluency(Node $node) : void
    {
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
    }
}
