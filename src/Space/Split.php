<?php
declare(strict_types=1);

namespace PhpStyler\Space;

use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;
use PhpStyler\Printable as P;

class Split extends Space
{
    protected const CLASS_RULE = [
        Expr\BinaryOp\BooleanAnd::class => 'bool_and',
        Expr\BinaryOp\BooleanOr::class => 'bool_or',
        Expr\BinaryOp\Coalesce::class => 'coalesce',
        Expr\BinaryOp\Concat::class => 'concat',
        Expr\Ternary::class => 'ternary',
        P\Args::class => 'args',
        P\Array_::class => 'array',
        P\AttributeArgs::class => 'attribute_args',
        P\ClosureParams::class => 'closure_params',
        P\Cond::class => 'cond',
        P\FunctionParams::class => 'function_params',
        P\InstanceCall::class => 'instance_call',
        P\InstanceProp::class => 'instance_prop',
        P\Precedence::class => 'precedence',
    ];

    public readonly string $rule;

    public readonly ?string $type;

    public readonly array $args;

    public function __construct(
        string $class,
        int $level = null,
        string $type = null,
        mixed ...$args,
    ) {
        $rule = self::CLASS_RULE[$class];

        if ($level !== null) {
            $rule .= '_' . $level;
        }

        $this->rule = $rule;
        $this->type = $type;
        $this->args = $args;
    }
}
