<?php
declare(strict_types=1);

namespace PhpStyler;

use BadMethodCallException;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;
use PhpStyler\Printable as P;
use PhpStyler\Printable\Printable;

class Styler
{
    protected Code $code;

    protected State $state;

    /**
     * @var array<class-string, string>
     */
    protected array $operators = [];

    /**
     * @param non-empty-string $eol
     */
    public function __construct(
        protected string $eol = "\n",
        protected int $lineLen = 88,
        protected int $indentLen = 4,
        protected bool $indentTab = false,
    ) {
        $this->setOperators();
    }

    protected function setOperators() : void
    {
        $this->operators = [
            Expr\Assign::class => [' ', '=', ' '],
            Expr\AssignOp\BitwiseAnd::class => [' ', '&=', ' '],
            Expr\AssignOp\BitwiseOr::class => [' ', '|=', ' '],
            Expr\AssignOp\BitwiseXor::class => [' ', '^=', ' '],
            Expr\AssignOp\Coalesce::class => [' ', '??=', ' '],
            Expr\AssignOp\Concat::class => [' ', '.=', ' '],
            Expr\AssignOp\Div::class => [' ', '/=', ' '],
            Expr\AssignOp\Minus::class => [' ', '-=', ' '],
            Expr\AssignOp\Mod::class => [' ', '%=', ' '],
            Expr\AssignOp\Mul::class => [' ', '*=', ' '],
            Expr\AssignOp\Plus::class => [' ', '+=', ' '],
            Expr\AssignOp\Pow::class => [' ', '**=', ' '],
            Expr\AssignOp\ShiftLeft::class => [' ', '<<=', ' '],
            Expr\AssignOp\ShiftRight::class => [' ', '>>=', ' '],
            Expr\AssignRef::class => [' ', '=&', ' '],
            Expr\BinaryOp\BitwiseAnd::class => [' ', '&', ' '],
            Expr\BinaryOp\BitwiseOr::class => [' ', '|', ' '],
            Expr\BinaryOp\BitwiseXor::class => [' ', '^', ' '],
            Expr\BinaryOp\BooleanAnd::class => [' ', '&&', ' '],
            Expr\BinaryOp\BooleanOr::class => [' ', '||', ' '],
            Expr\BinaryOp\Coalesce::class => [' ', '??', ' '],
            Expr\BinaryOp\Concat::class => [' ', '.', ' '],
            Expr\BinaryOp\Div::class => [' ', '/', ' '],
            Expr\BinaryOp\Equal::class => [' ', '==', ' '],
            Expr\BinaryOp\Greater::class => [' ', '>', ' '],
            Expr\BinaryOp\GreaterOrEqual::class => [' ', '>=', ' '],
            Expr\BinaryOp\Identical::class => [' ', '===', ' '],
            Expr\BinaryOp\LogicalAnd::class => [' ', 'and', ' '],
            Expr\BinaryOp\LogicalOr::class => [' ', 'or', ' '],
            Expr\BinaryOp\LogicalXor::class => [' ', 'xor', ' '],
            Expr\BinaryOp\Minus::class => [' ', '-', ' '],
            Expr\BinaryOp\Mod::class => [' ', '%', ' '],
            Expr\BinaryOp\Mul::class => [' ', '*', ' '],
            Expr\BinaryOp\NotEqual::class => [' ', '!=', ' '],
            Expr\BinaryOp\NotIdentical::class => [' ', '!==', ' '],
            Expr\BinaryOp\Plus::class => [' ', '+', ' '],
            Expr\BinaryOp\Pow::class => [' ', '**', ' '],
            Expr\BinaryOp\ShiftLeft::class => [' ', '<<', ' '],
            Expr\BinaryOp\ShiftRight::class => [' ', '>>', ' '],
            Expr\BinaryOp\Smaller::class => [' ', '<', ' '],
            Expr\BinaryOp\SmallerOrEqual::class => [' ', '<=', ' '],
            Expr\BinaryOp\Spaceship::class => [' ', '<=>', ' '],
            Expr\BitwiseNot::class => ['', '~', ' '],
            Expr\BooleanNot::class => ['', '!', ' '],
            Expr\ErrorSuppress::class => ['', '@', ''],
            Expr\Instanceof_::class => [' ', 'instanceof', ' '],
            Expr\PostDec::class => [' ', '--', ''],
            Expr\PostInc::class => [' ', '++', ''],
            Expr\PreDec::class => ['', '--', ' '],
            Expr\PreInc::class => ['', '++', ' '],
            Expr\Print_::class => ['', 'print', ' '],
            Expr\Ternary::class => [' ', '?:', ' '],
            Expr\UnaryMinus::class => ['', '-', ''],
            Expr\UnaryPlus::class => ['', '+', ''],
            Expr\YieldFrom::class => ['', 'yield from', ' '],
        ];
    }

    /**
     * @param array<int, null|string|Printable> $list
     */
    public function __invoke(array $list) : string
    {
        if (! $list) {
            return "<?php" . $this->eol;
        }

        $this->code = $this->newCode();
        $this->state = $this->newState();

        while ($list) {
            $p = array_shift($list);
            $this->s($p);
        }

        $this->commit();
        return $this->finish($this->code->getFile());
    }

    protected function newCode() : Code
    {
        return new Code(
            $this->eol,
            $this->lineLen,
            $this->indentTab ? "\t" : str_pad('', $this->indentLen),
            $this->indentLen,
        );
    }

    protected function finish(string $code) : string
    {
        return "<?php" . $this->eol . trim($code) . $this->eol;
    }

    protected function newState() : State
    {
        return new State();
    }

    protected function commit() : void
    {
        $this->code->commit();
    }

    protected function indent() : void
    {
        $this->code[] = new Space\Indent();
    }

    protected function outdent() : void
    {
        $this->code[] = new Space\Outdent();
    }

    protected function clip() : void
    {
        $this->code[] = new Space\Clip();
    }

    protected function condense() : void
    {
        $this->code[] = new Space\Condense();
    }

    protected function clipToParen() : void
    {
        $this->code[] = new Space\ClipToParen();
    }

    protected function newline() : void
    {
        $this->code[] = new Space\Newline();
    }

    protected function split(
        string $class,
        int $level = null,
        string $type = null,
        mixed ...$args,
    ) : void
    {
        if (! $this->state->encapsed) {
            $this->code->addSplit($class, $level, $type, ...$args);
        }
    }

    protected function force(
        string $class,
        int $level = null,
        string $type = null,
        mixed ...$args,
    ) : void
    {
        $this->code->forceSplit($class, $level, $type, ...$args);
    }

    protected function modifiers(?int $flags) : string
    {
        return ($flags & Stmt\Class_::MODIFIER_FINAL ? 'final ' : '')
            . ($flags & Stmt\Class_::MODIFIER_ABSTRACT ? 'abstract ' : '')
            . ($flags & Stmt\Class_::MODIFIER_PUBLIC ? 'public ' : '')
            . ($flags & Stmt\Class_::MODIFIER_PROTECTED ? 'protected ' : '')
            . ($flags & Stmt\Class_::MODIFIER_PRIVATE ? 'private ' : '')
            . ($flags & Stmt\Class_::MODIFIER_STATIC ? 'static ' : '')
            . ($flags & Stmt\Class_::MODIFIER_READONLY ? 'readonly ' : '');
    }

    protected function maybeNewline(Printable $p) : void
    {
        if ($p->isFirst() || $p->hasComment() || $p->hasAttribute()) {
            return;
        }

        $this->condense();
        $this->newline();
        $this->commit();
    }

    protected function s(null|string|Printable $p) : void
    {
        if ($p === null) {
            return;
        }

        if ($p instanceof Printable) {
            $this->sPrintable($p);
            return;
        }

        if ($this->state->heredoc) {
            $this->sHeredocBody($p);
            return;
        }

        $this->code[] = $p;
    }

    protected function sPrintable(Printable $p) : void
    {
        // first printable in body?
        $p->isFirst($this->state->atFirstInBody);
        $this->state->atFirstInBody = false;

        // has comment?
        $p->hasComment($this->state->hadComment);
        $this->state->hadComment = false;

        // has attribute?
        $p->hasAttribute($this->state->hadAttribute);
        $this->state->hadAttribute = false;

        // add the printable to the code
        $last = (string) strrchr(get_class($p), '\\');
        $method = 's' . trim($last, '\\_');
        $this->{$method}($p);
    }

    protected function sArgs(P\Args $p) : void
    {
        $this->state->args ++;
        $this->code[] = '(';

        if ($p->hasExpansiveArg) {
            $this->state->argsHaveNewOrClosure ++;
            $this->force(P\Args::class, -1 * $this->state->args);
        } elseif ($p->count) {
            $this->split(P\Args::class, $this->state->args);
        }
    }

    // now the problem is, how so split on separator with `args_-1` et al?
    // track in State? and then maybe we don't need to force at all.
    protected function sArgSeparator(P\Separator $p) : void
    {
        $this->clip();
        $this->code[] = ', ';
        $level = $this->state->args;
        $level *= $this->state->argsHaveNewOrClosure ? -1 : 1;
        $this->split(P\Args::class, $level, 'mid');
    }

    protected function sArgsEnd(P\ArgsEnd $p) : void
    {
        if ($p->hasExpansiveArg) {
            $this->force(P\Args::class, -1 * $this->state->args, 'end', ',');
            $this->state->argsHaveNewOrClosure --;
        } elseif ($p->count) {
            $this->split(P\Args::class, $this->state->args, 'end', ',');
        }

        $this->code[] = ')';
        $this->state->args --;
    }

    protected function sArray(P\Array_ $p) : void
    {
        $this->state->array ++;
        $this->code[] = '[';
        $this->state->atFirstInBody = true;

        if ($p->count) {
            $this->split(P\Array_::class, $this->state->array);
        }
    }

    protected function sArraySeparator(P\Separator $p) : void
    {
        $this->clip();
        $this->code[] = ', ';
        $this->split(P\Array_::class, $this->state->array, 'mid');
    }

    protected function sArrayEnd(P\ArrayEnd $p) : void
    {
        if ($p->count) {
            $this->split(P\Array_::class, $this->state->array, 'end', ',');
        }

        $this->code[] = ']';
        $this->state->array --;
    }

    protected function sArrayDim(P\ArrayDim $p) : void
    {
        $this->state->array ++;
        $this->code[] = '[';
    }

    protected function sArrayDimEnd(P\End $p) : void
    {
        $this->code[] = ']';
        $this->state->array --;
    }

    protected function sArrowFunction(P\ArrowFunction $p) : void
    {
        $this->code[] = $p->static ? 'static fn ' : 'fn ';
    }

    protected function sArrowFunctionEnd(P\End $p) : void
    {
        $this->clip();
    }

    protected function sAs(P\As_ $p) : void
    {
        $this->code[] = ' as ';
    }

    protected function sAttributeGroups(P\AttributeGroups $p) : void
    {
        if ($this->state->params) {
            return;
        }

        $this->condense();

        if (! $p->hasComment()) {
            $this->newline();
        }
    }

    protected function sAttributeGroup(P\AttributeGroup $p) : void
    {
        $this->code[] = '#[';
    }

    protected function sAttributeArgs(P\AttributeArgs $p) : void
    {
        $this->state->attrArgs ++;
        $this->code[] = '(';

        if ($p->count) {
            $this->split(P\AttributeArgs::class, $this->state->attrArgs);
        }
    }

    protected function sAttributeArgSeparator(P\Separator $p) : void
    {
        $this->code[] = ', ';
        $this->split(P\AttributeArgs::class, $this->state->attrArgs, 'mid', ', ');
    }

    protected function sAttributeArgsEnd(P\AttributeArgsEnd $p) : void
    {
        if ($p->count) {
            $this->split(P\AttributeArgs::class, $this->state->attrArgs, 'end', ',');
        }

        $this->code[] = ')';
        $this->state->attrArgs --;
    }

    protected function sAttributeGroupEnd(P\End $p) : void
    {
        $this->code[] = ']';

        if ($this->state->params) {
            $this->code[] = ' ';
            $this->split(P\AttributeArgs::class, $this->state->attrArgs, 'mid', '');
        } else {
            $this->commit();
        }
    }

    protected function sAttributeGroupsEnd(P\End $p) : void
    {
        $this->state->hadAttribute = true;
    }

    protected function sBody(P\Body $p) : void
    {
        $this->state->atFirstInBody = true;
        $method = 's' . ucfirst($p->type) . 'Body';
        $this->{$method}($p);
    }

    protected function sBodyEnd(P\BodyEnd $p) : void
    {
        $method = 's' . ucfirst($p->type) . 'BodyEnd';
        $this->{$method}($p);
    }

    protected function sBodyEmpty(P\BodyEmpty $p) : void
    {
        $method = 's' . ucfirst($p->type) . 'BodyEmpty';
        $this->{$method}($p);
    }

    protected function sBreak(P\Break_ $p) : void
    {
        $this->code[] = rtrim('break ' . $p->num) . ';';
        $this->newline();
        $this->commit();
    }

    protected function sCast(P\Cast $p) : void
    {
        $this->code[] = '(' . $p->type . ') ';
    }

    protected function sClass(P\Class_ $p) : void
    {
        if ($p->name) {
            $this->maybeNewline($p);
        }

        $name = $p->name ? ' ' . $p->name : ' ';
        $this->code[] = $this->modifiers($p->flags) . 'class' . $name;
    }

    protected function sClassBody(P\Body $p) : void
    {
        $this->newline();
        $this->code[] = '{';
        $this->indent();
        $this->commit();
        $this->condense();
    }

    protected function sClassBodyEnd(P\BodyEnd $p) : void
    {
        $this->outdent();
        $this->condense();
        $this->code[] = '}';
        $this->newline();
        $this->commit();
    }

    protected function sClosure(P\Closure $p) : void
    {
        $this->code[] = $p->static ? 'static function ' : 'function ';
    }

    protected function sClosureUse(P\ClosureUse $p) : void
    {
        $this->state->params ++;
        $this->code[] = ' use (';

        if ($p->count) {
            $this->split(P\Params::class, $this->state->params);
        }
    }

    protected function sClosureUseEnd(P\ClosureUseEnd $p) : void
    {
        if ($p->count) {
            $this->split(P\Params::class, $this->state->params, 'end', ',');
        }

        $this->code[] = ')';
        $this->state->params --;
    }

    protected function sClosureBody(P\Body $p) : void
    {
        $this->code[] = ' {';
        $this->indent();

        if ($this->state->args) {
            $this->condense();
        } else {
            $this->commit();
        }
    }

    protected function sClosureBodyEnd(P\BodyEnd $p) : void
    {
        $this->outdent();
        $this->condense();
        $this->code[] = '}';
    }

    protected function sClosureBodyEmpty(P\BodyEmpty $p) : void
    {
        $this->code[] = ' {}';
    }

    protected function sContinue(P\Continue_ $p) : void
    {
        $this->code[] = rtrim('continue ' . $p->num) . ';';
        $this->commit();
    }

    protected function sComments(P\Comments $p) : void
    {
        $this->condense();

        if (! $p->isFirst()) {
            $this->commentNewline();
        }
    }

    protected function sComment(P\Comment $p) : void
    {
        $this->code[] = $p->text;
        $this->state->hadComment = true;
        $this->commentNewline();
    }

    protected function commentNewline() : void
    {
        if ($this->state->cond || $this->state->args || $this->state->array) {
            $this->newline();
        } else {
            $this->commit();
        }
    }

    protected function sCond(P\Cond $p) : void
    {
        $this->state->cond ++;
        $this->code[] = '(';
        $this->split(P\Cond::class);
    }

    protected function sCondEnd(P\End $p) : void
    {
        $this->split(P\Cond::class, null, 'end');
        $this->code[] = ')';
        $this->state->cond --;
    }

    protected function sConst(P\Const_ $p) : void
    {
        $this->code[] = 'const ';
    }

    protected function sConstEnd(P\End $p) : void
    {
        $this->clip();
        $this->code[] = ';';
        $this->newline();
        $this->commit();
    }

    public function sDeclare(P\Declare_ $p) : void
    {
        $this->code[] = 'declare';
    }

    public function sDeclareBody(P\Body $p) : void
    {
        $this->code[] = ' {';
        $this->indent();
        $this->commit();
    }

    public function sDeclareBodyEnd(P\BodyEnd $p) : void
    {
        $this->outdent();
        $this->condense();
        $this->code[] = '}';
        $this->commit();
    }

    public function sDeclareBodyEmpty(P\BodyEmpty $p) : void
    {
        $this->code[] = ';';
        $this->newline();
        $this->commit();
    }

    public function sDeclareDirective(P\DeclareDirective $p) : void
    {
        $this->code[] = $p->name . '=';
    }

    protected function sDoBody(P\Body $p) : void
    {
        $this->maybeNewline($p);
        $this->code[] = 'do {';
        $this->indent();
        $this->commit();
    }

    protected function sDoBodyEnd(P\BodyEnd $p) : void
    {
        $this->outdent();
        $this->condense();
        $this->code[] = '} while ';
    }

    protected function sDoEnd(P\End $p) : void
    {
        $this->code[] = ';';
        $this->newline();
        $this->commit();
    }

    protected function sDoubleArrow(P\DoubleArrow $p) : void
    {
        $this->code[] = ' => ';
    }

    protected function sEncapsed(P\Encapsed $p) : void
    {
        $this->state->encapsed ++;
    }

    protected function sEncapsedEnd(P\End $p) : void
    {
        $this->state->encapsed --;
    }

    protected function sEnd(P\End $p) : void
    {
        $method = 's' . ucfirst($p->type) . 'End';
        $this->{$method}($p);
    }

    protected function sEnum(P\Enum_ $p) : void
    {
        $this->maybeNewline($p);
        $this->code[] = 'enum ' . $p->name;
    }

    protected function sEnumBody(P\Body $p) : void
    {
        $this->newline();
        $this->code[] = '{';
        $this->indent();
        $this->commit();
    }

    protected function sEnumCase(P\EnumCase $p) : void
    {
        $this->code[] = 'case ' . $p->name;
    }

    protected function sEnumCaseEnd(P\End $p) : void
    {
        $this->code[] = ';';
        $this->newline();
        $this->commit();
    }

    protected function sEnumBodyEnd(P\BodyEnd $p) : void
    {
        $this->outdent();
        $this->condense();
        $this->code[] = '}';
        $this->newline();
        $this->commit();
    }

    protected function sExprEnd(P\End $p) : void
    {
        $this->clip();
        $this->code[] = ';';
        $this->commit();
    }

    protected function sExtends(P\Extends_ $extends) : void
    {
        $this->code[] = ' extends ' . $extends->name;
    }

    protected function sFalse(P\False_ $p) : void
    {
        $this->code[] = 'false';
    }

    protected function sFor(P\For_ $p) : void
    {
        $this->maybeNewline($p);
        $this->code[] = 'for ';
    }

    protected function sForBody(P\Body $p) : void
    {
        $this->code[] = ' {';
        $this->indent();
        $this->commit();
    }

    protected function sForBodyEnd(P\BodyEnd $p) : void
    {
        $this->outdent();
        $this->condense();
        $this->code[] = '}';
        $this->newline();
        $this->commit();
    }

    protected function sForExprSeparator(P\Separator $p) : void
    {
        $this->code[] = '; ';
        $this->split(P\Args::class, $this->state->args, 'mid');
    }

    protected function sForeach(P\Foreach_ $p) : void
    {
        $this->maybeNewline($p);
        $this->code[] = 'foreach ';
    }

    protected function sForeachBody(P\Body $p) : void
    {
        $this->code[] = ' {';
        $this->indent();
        $this->commit();
    }

    protected function sForeachBodyEnd(P\BodyEnd $p) : void
    {
        $this->outdent();
        $this->condense();
        $this->code[] = '}';
        $this->newline();
        $this->commit();
    }

    protected function sFunction(P\Function_ $p) : void
    {
        $this->code[] = $this->modifiers($p->flags) . 'function ';
    }

    protected function sFunctionBodyEmpty(P\BodyEmpty $p) : void
    {
        $this->code[] = ';';
        $this->newline();
        $this->commit();
    }

    protected function sFunctionBody(P\Body $p) : void
    {
        $this->newline();
        $this->clipToParen();
        $this->code[] = '{';
        $this->indent();
        $this->commit();
    }

    protected function sFunctionBodyEnd(P\BodyEnd $p) : void
    {
        $this->outdent();
        $this->condense();
        $this->code[] = '}';
        $this->newline();
        $this->commit();
    }

    protected function sGoto(P\Goto_ $p) : void
    {
        $this->code[] = "goto {$p->label};";
        $this->commit();
    }

    protected function sHaltCompiler(P\HaltCompiler $p) : void
    {
        $this->code[] = '__halt_compiler();';
    }

    protected function sHeredoc(P\Heredoc $p) : void
    {
        $this->code[] = "<<<{$p->label}";
        $this->state->heredoc ++;
        $this->newline();
    }

    protected function sHeredocBody(string $p) : void
    {
        $lines = explode($this->eol, $p);
        $this->code[] = array_shift($lines);

        foreach ($lines as $line) {
            $this->newline();
            $this->code[] = $line;
        }
    }

    protected function sHeredocEnd(P\HeredocEnd $p) : void
    {
        $this->newline();
        $this->state->heredoc --;
        $this->code[] = $p->label;
    }

    protected function sIf(P\If_ $p) : void
    {
        $this->maybeNewline($p);
        $this->code[] = 'if ';
    }

    protected function sIfBody(P\Body $p) : void
    {
        $this->code[] = ' {';
        $this->indent();
        $this->commit();
    }

    protected function sElseIf(P\ElseIf_ $p) : void
    {
        $this->outdent();
        $this->condense();
        $this->code[] = '} elseif ';
    }

    protected function sElseIfBody(P\Body $p) : void
    {
        $this->code[] = ' {';
        $this->indent();
        $this->commit();
    }

    protected function sElse(P\Else_ $p) : void
    {
        $this->outdent();
        $this->condense();
        $this->code[] = '} else ';
    }

    protected function sElseBody(P\Body $p) : void
    {
        $this->code[] = '{';
        $this->indent();
        $this->commit();
    }

    protected function sIfEnd(P\End $p) : void
    {
        $this->outdent();
        $this->condense();
        $this->code[] = '}';
        $this->newline();
        $this->commit();
    }

    protected function sImplements(P\Implements_ $p) : void
    {
        $this->code[] = ' implements ';
        $this->split(P\Implements_::class, null, 'condense');
    }

    protected function sImplementsSeparator(P\Separator $p) : void
    {
        $this->clip();
        $this->code[] = ', ';
        $this->split(P\Implements_::class, null, 'mid');
    }

    protected function sImplementsEnd(P\End $p) : void
    {
        $this->split(P\Implements_::class, null, 'endCondense');
        $this->clip();
    }

    protected function sInfix(P\Infix $p) : void
    {
    }

    /**
     * Handles line split for `&&`, `||`, `.`, and `?:`.
     */
    protected function sInfixOp(P\InfixOp $p) : void
    {
        $this->code[] = $this->operators[$p->class][0];

        switch ($p->class) {
            case Expr\BinaryOp\BooleanAnd::class:
            case Expr\BinaryOp\BooleanOr::class:
            case Expr\BinaryOp\LogicalAnd::class:
            case Expr\BinaryOp\LogicalOr::class:
                if (! $this->state->cond) {
                    $this->split($p->class, null, 'condense');
                } else {
                    $this->split($p->class, null, 'mid');
                }

                break;

            case Expr\BinaryOp\Coalesce::class:
                if (! $this->state->args) {
                    $this->split($p->class, null, 'condense');
                    $this->split($p->class, null, 'outdent');
                }

                break;

            case Expr\BinaryOp\Concat::class:
                $this->split($p->class, null, 'condense');
                break;

            case Expr\Ternary::class:
                if (! $this->state->args) {
                    $this->split($p->class, null, 'condense');
                }

                break;
        }

        $this->code[] = $this->operators[$p->class][1] . $this->operators[$p->class][2];
    }

    protected function sInfixEnd(P\InfixEnd $p) : void
    {
        switch ($p->class) {
            case Expr\BinaryOp\BooleanAnd::class:
            case Expr\BinaryOp\BooleanOr::class:
            case Expr\BinaryOp\LogicalAnd::class:
            case Expr\BinaryOp\LogicalOr::class:
                if (! $this->state->cond) {
                    $this->split($p->class, null, 'endCondense');
                }

                break;

            case Expr\BinaryOp\Coalesce::class:
                if (! $this->state->args) {
                    $this->split($p->class, null, 'mid');
                }

                break;

            case Expr\BinaryOp\Concat::class:
                $this->split($p->class, null, 'endCondense');
                break;

            case Expr\Ternary::class:
                if (! $this->state->args) {
                    $this->split($p->class, null, 'endCondense');
                }

                break;
        }
    }

    protected function sInlineHtml(P\InlineHtml $p) : void
    {
        $this->code[] = '?>' . ($p->newline ? $this->eol : '');
    }

    protected function sInlineHtmlEnd(P\End $p) : void
    {
        $this->code[] = '<?php';
        $this->newline();
    }

    protected function sInterface(P\Interface_ $p) : void
    {
        $this->maybeNewline($p);
        $this->code[] = 'interface ' . $p->name;
    }

    protected function sInterfaceBody(P\Body $p) : void
    {
        $this->newline();
        $this->code[] = '{';
        $this->indent();
        $this->commit();
    }

    protected function sInterfaceBodyEnd(P\BodyEnd $p) : void
    {
        $this->outdent();
        $this->condense();
        $this->code[] = '}';
        $this->newline();
        $this->commit();
    }

    protected function sLabel(P\Label $p) : void
    {
        $this->newline();
        $this->code[] = "{$p->name}:";
        $this->commit();
    }

    protected function sMatch(P\Match_ $p) : void
    {
        $this->code[] = 'match ';
    }

    protected function sMatchBody(P\Body $p) : void
    {
        $this->code[] = ' {';
        $this->indent();
        $this->commit();
    }

    protected function sMatchSeparator(P\Separator $p) : void
    {
        $this->clip();
        $this->code[] = ', ';
        $this->split(P\Args::class, $this->state->args, 'mid');
    }

    protected function sMatchArmEnd(P\End $p) : void
    {
        $this->code[] = ',';
        $this->commit();
    }

    protected function sMatchBodyEnd(P\BodyEnd $p) : void
    {
        $this->outdent();
        $this->condense();
        $this->code[] = '}';
    }

    protected function sStaticMember(P\StaticMember $p) : void
    {
        $this->code[] = $p->operator;
    }

    protected function sInstanceOp(P\InstanceOp $p) : void
    {
        if (! $this->state->inArgsOrArray() && $p->isFluent()) {
            $this->state->instanceOp ++;
            $this->split(P\InstanceOp::class, $this->state->instanceOp, 'condense');
        }

        $this->code[] = $p->str;
    }

    protected function sInstanceOpEnd(P\InstanceOpEnd $p) : void
    {
        if (! $this->state->inArgsOrArray() && $p->isFluent()) {
            $this->split(P\InstanceOp::class, $this->state->instanceOp, 'endCondense');
            $this->state->instanceOp --;
        }
    }

    protected function sModifiers(P\Modifiers $modifiers) : void
    {
        $this->code[] = $this->modifiers($modifiers->flags);
    }

    protected function sNamespace(P\Namespace_ $p) : void
    {
        $this->code[] = 'namespace';

        if ($p->name) {
            $this->code[] = ' ' . $p->name;
        }
    }

    protected function sNamespaceBody(P\Body $p) : void
    {
        $this->newline();
        $this->code[] = '{';
        $this->indent();
        $this->commit();
    }

    protected function sNamespaceBodyEnd(P\BodyEnd $p) : void
    {
        $this->outdent();
        $this->condense();
        $this->code[] = '}';
        $this->newline();
        $this->commit();
    }

    protected function sNamespaceBodyEmpty(P\BodyEmpty $p) : void
    {
        $this->code[] = ';';
        $this->newline();
        $this->commit();
    }

    protected function sNew(P\New_ $p) : void
    {
        $this->code[] = 'new ';
    }

    protected function sNowdoc(P\Nowdoc $p) : void
    {
        $this->code[] = "<<<'{$p->label}'";
        $this->newline();

        foreach (explode($this->eol, $p->value) as $line) {
            $this->code[] = $line;
            $this->newline();
        }

        $this->code[] = $p->label;
        $this->newline();
    }

    protected function sNull(P\Null_ $p) : void
    {
        $this->code[] = 'null';
    }

    protected function sParamName(P\ParamName $p) : void
    {
        $this->code[] = $p->name . ': ';
    }

    protected function sParams(P\Params $p) : void
    {
        $this->state->params ++;
        $this->code[] = '(';

        if ($p->count) {
            $this->split(P\Params::class, $this->state->params);
        }
    }

    protected function sParamSeparator(P\Separator $p) : void
    {
        $this->code[] = ', ';
        $this->split(P\Params::class, $this->state->params, 'mid');
    }

    protected function sParamsEnd(P\ParamsEnd $p) : void
    {
        if ($p->count) {
            $this->split(P\Params::class, $this->state->params, 'end', ',');
        }

        $this->code[] = ')';
        $this->state->params --;
    }

    protected function sPostfixOp(P\PostfixOp $p) : void
    {
        $this->code[] = $this->operators[$p->class][0]
            . $this->operators[$p->class][1]
            . $this->operators[$p->class][2];
    }

    protected function sPrecedence(P\Precedence $p) : void
    {
        $this->code[] = '(';

        if (! $p->ternary) {
            $this->split(P\Precedence::class, null, 'condense');
        }
    }

    protected function sPrecedenceEnd(P\PrecedenceEnd $p) : void
    {
        if (! $p->ternary) {
            $this->split(P\Precedence::class, null, 'endCondense');
        }

        $this->code[] = ')';
    }

    protected function sPrefixOp(P\PrefixOp $p) : void
    {
        $this->code[] = $this->operators[$p->class][0]
            . $this->operators[$p->class][1]
            . $this->operators[$p->class][2];
    }

    protected function sProperty(P\Property $p) : void
    {
        $this->maybeNewline($p);
        $this->code[] = $this->modifiers($p->flags);
    }

    protected function sPropertyEnd(P\End $end) : void
    {
        $this->clip();
        $this->code[] = ';';
        $this->newline();
        $this->commit();
    }

    protected function sReturn(P\Return_ $p) : void
    {
        $this->code[] = $p->isEmpty ? 'return' : 'return ';
    }

    protected function sReturnEnd(P\End $p) : void
    {
        $this->clip();
        $this->code[] = ';';
        $this->commit();
    }

    protected function sReturnType(P\ReturnType $p) : void
    {
        $this->code[] = ' : ';
    }

    protected function sTrue(P\True_ $p) : void
    {
        $this->code[] = 'true';
    }

    protected function sReservedArg(P\ReservedArg $p) : void
    {
        $this->code[] = '(';
    }

    protected function sReservedArgEnd(P\End $p) : void
    {
        $this->code[] = ')';
    }

    protected function sReservedStmt(P\ReservedStmt $p) : void
    {
        $this->code[] = $p->name . ' ';
    }

    protected function sReservedStmtEnd(P\End $p) : void
    {
        $this->clip();
        $this->code[] = ';';
        $this->commit();
    }

    protected function sReservedWord(P\ReservedWord $p) : void
    {
        $this->code[] = $p->name . ' ';
    }

    protected function sSeparator(P\Separator $p) : void
    {
        $method = 's' . ucfirst($p->type) . 'Separator';

        if (method_exists($this, $method)) {
            $this->{$method}($p);
            return;
        }

        $this->code[] = ', ';
    }

    protected function sSwitch(P\Switch_ $p) : void
    {
        $this->maybeNewline($p);
        $this->code[] = 'switch ';
    }

    protected function sSwitchBody(P\Body $p) : void
    {
        $this->code[] = ' {';
        $this->indent();
        $this->commit();
    }

    protected function sSwitchCase(P\SwitchCase $p) : void
    {
        $this->code[] = 'case ';
    }

    protected function sSwitchCaseDefault(P\SwitchCaseDefault $p) : void
    {
        $this->code[] = 'default';
    }

    protected function sSwitchCaseEnd(P\SwitchCaseEnd $p) : void
    {
        $this->code[] = ':';

        if ($p->hasBody) {
            $this->indent();
        }

        $this->commit();
    }

    protected function sSwitchCaseBody(P\Body $p) : void
    {
        $this->condense();
    }

    protected function sSwitchCaseBodyEnd(P\BodyEnd $p) : void
    {
        $this->outdent();
        $this->condense();
        $this->commit();
    }

    protected function sSwitchBodyEnd(P\BodyEnd $p) : void
    {
        $this->outdent();
        $this->condense();
        $this->code[] = '}';
        $this->newline();
        $this->commit();
    }

    /**
     * Handles `$var ? $true : $false`.
     *
     * @see sInfixOp() for `$var ?: $false` handling.
     */
    protected function sTernary(P\Ternary $p) : void
    {
        $this->code[] = ' ';

        if (! $this->state->args) {
            $this->split(Expr\Ternary::class, null, 'condense');
        }

        $this->code[] = $p->operator . ' ';
    }

    protected function sTernaryEnd(P\End $p) : void
    {
        if (! $this->state->args) {
            $this->split(Expr\Ternary::class, null, 'endCondense');
        }
    }

    protected function sThrow(P\Throw_ $p) : void
    {
        $this->maybeNewline($p);
        $this->code[] = 'throw ';
    }

    protected function sThrowEnd(P\End $p) : void
    {
        $this->clip();
        $this->code[] = ';';
        $this->commit();
    }

    protected function sTrait(P\Trait_ $p) : void
    {
        $this->maybeNewline($p);
        $this->code[] = 'trait ' . $p->name;
    }

    protected function sTraitBody(P\Body $p) : void
    {
        $this->newline();
        $this->code[] = '{';
        $this->indent();
        $this->commit();
    }

    protected function sTraitBodyEnd(P\BodyEnd $p) : void
    {
        $this->outdent();
        $this->condense();
        $this->code[] = '}';
        $this->newline();
        $this->commit();
    }

    protected function sTry(P\Try_ $p) : void
    {
        $this->maybeNewline($p);
        $this->code[] = 'try';
    }

    protected function sTryBody(P\Body $p) : void
    {
        $this->code[] = ' {';
        $this->indent();
        $this->commit();
    }

    protected function sTryCatch(P\TryCatch $p) : void
    {
        $this->outdent();
        $this->condense();
        $this->code[] = '} catch ';
    }

    protected function sTryCatchBody(P\Body $p) : void
    {
        $this->code[] = ' {';
        $this->indent();
        $this->commit();
    }

    protected function sTryFinally(P\TryFinally $p) : void
    {
        $this->outdent();
        $this->condense();
        $this->code[] = '} finally ';
    }

    protected function sTryFinallyBody(P\Body $p) : void
    {
        $this->code[] = '{';
        $this->indent();
        $this->commit();
    }

    protected function sTryEnd(P\End $p) : void
    {
        $this->outdent();
        $this->condense();
        $this->code[] = '}';
        $this->newline();
        $this->commit();
    }

    protected function sUnset(P\Unset_ $unset) : void
    {
        $this->code[] = 'unset(';
    }

    protected function sUnsetEnd(P\End $end) : void
    {
        $this->code[] = ');';
        $this->commit();
    }

    protected function sUseImport(P\UseImport $p) : void
    {
        $this->code[] = 'use ' . ($p->type ? $p->type . ' ' : '');

        if ($p->prefix) {
            $this->code[] = $p->prefix . '\\{';
        }
    }

    protected function sUseImportEnd(P\UseImportEnd $p) : void
    {
        if ($p->prefix) {
            $this->code[] = '}';
        }

        $this->clip();
        $this->code[] = ';';
        $this->commit();
    }

    protected function sUseTrait(P\UseTrait $p) : void
    {
        $this->code[] = 'use ';
    }

    protected function sUseTraitBody(P\Body $p) : void
    {
        $this->code[] = ' {';
        $this->indent();
        $this->commit();
    }

    protected function sUseTraitBodyEnd(P\BodyEnd $p) : void
    {
        $this->outdent();
        $this->condense();
        $this->code[] = '}';
        $this->commit();
    }

    protected function sUseTraitAs(P\UseTraitAs $p) : void
    {
        if ($p->trait) {
            $this->code[] = $p->trait . '::';
        }

        $this->code[] = $p->oldName . ' as';

        if ($p->flags) {
            $this->code[] = ' ' . rtrim($this->modifiers($p->flags), ' ');
        }

        if ($p->newName) {
            $this->code[] = ' ' . $p->newName;
        }

        $this->code[] = ';';
        $this->commit();
    }

    protected function sUseTraitInsteadof(P\UseTraitInsteadof $p) : void
    {
        $this->code[] = $p->trait;
        $this->code[] = '::' . $p->method . ' insteadof ';
    }

    protected function sUseTraitInsteadOfEnd(P\End $p) : void
    {
        $this->code[] = ';';
        $this->commit();
    }

    protected function sUseTraitEnd(P\End $p) : void
    {
        $this->code[] = ';';
        $this->commit();
    }

    protected function sWhile(P\While_ $p) : void
    {
        $this->maybeNewline($p);
        $this->code[] = 'while ';
    }

    protected function sWhileBody(P\Body $p) : void
    {
        $this->code[] = ' {';
        $this->indent();
        $this->commit();
    }

    protected function sWhileBodyEnd(P\BodyEnd $p) : void
    {
        $this->outdent();
        $this->condense();
        $this->code[] = '}';
        $this->newline();
        $this->commit();
    }

    protected function sYield(P\Yield_ $p) : void
    {
        $this->code[] = $p->isEmpty ? 'yield' : 'yield ';
    }
}
