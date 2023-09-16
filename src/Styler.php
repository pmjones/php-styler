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
    protected int $indentNum = 0;

    protected Line $line;

    /**
     * @var Line[]
     */
    protected array $lines = [];

    protected Nesting $nesting;

    /**
     * @var array<class-string, array{string, string, string}>
     */
    protected array $operators = [];

    public bool $atFirstInBody = false;

    public bool $hadAttribute = false;

    public bool $hadComment = false;

    /**
     * @param non-empty-string $eol
     */
    public function __construct(
        protected string $eol = "\n",
        protected int $lineLen = 88,
        protected int $indentLen = 4,
        protected bool $indentTab = false,
    ) {
        $this->operators[Expr\Assign::class] = [' ', '=', ' '];
        $this->operators[Expr\AssignOp\BitwiseAnd::class] = [' ', '&=', ' '];
        $this->operators[Expr\AssignOp\BitwiseOr::class] = [' ', '|=', ' '];
        $this->operators[Expr\AssignOp\BitwiseXor::class] = [' ', '^=', ' '];
        $this->operators[Expr\AssignOp\Coalesce::class] = [' ', '??=', ' '];
        $this->operators[Expr\AssignOp\Concat::class] = [' ', '.=', ' '];
        $this->operators[Expr\AssignOp\Div::class] = [' ', '/=', ' '];
        $this->operators[Expr\AssignOp\Minus::class] = [' ', '-=', ' '];
        $this->operators[Expr\AssignOp\Mod::class] = [' ', '%=', ' '];
        $this->operators[Expr\AssignOp\Mul::class] = [' ', '*=', ' '];
        $this->operators[Expr\AssignOp\Plus::class] = [' ', '+=', ' '];
        $this->operators[Expr\AssignOp\Pow::class] = [' ', '**=', ' '];
        $this->operators[Expr\AssignOp\ShiftLeft::class] = [' ', '<<=', ' '];
        $this->operators[Expr\AssignOp\ShiftRight::class] = [' ', '>>=', ' '];
        $this->operators[Expr\AssignRef::class] = [' ', '=&', ' '];
        $this->operators[Expr\BinaryOp\BitwiseAnd::class] = [' ', '&', ' '];
        $this->operators[Expr\BinaryOp\BitwiseOr::class] = [' ', '|', ' '];
        $this->operators[Expr\BinaryOp\BitwiseXor::class] = [' ', '^', ' '];
        $this->operators[Expr\BinaryOp\BooleanAnd::class] = [' ', '&&', ' '];
        $this->operators[Expr\BinaryOp\BooleanOr::class] = [' ', '||', ' '];
        $this->operators[Expr\BinaryOp\Coalesce::class] = [' ', '??', ' '];
        $this->operators[Expr\BinaryOp\Concat::class] = [' ', '.', ' '];
        $this->operators[Expr\BinaryOp\Div::class] = [' ', '/', ' '];
        $this->operators[Expr\BinaryOp\Equal::class] = [' ', '==', ' '];
        $this->operators[Expr\BinaryOp\Greater::class] = [' ', '>', ' '];
        $this->operators[Expr\BinaryOp\GreaterOrEqual::class] = [' ', '>=', ' '];
        $this->operators[Expr\BinaryOp\Identical::class] = [' ', '===', ' '];
        $this->operators[Expr\BinaryOp\LogicalAnd::class] = [' ', 'and', ' '];
        $this->operators[Expr\BinaryOp\LogicalOr::class] = [' ', 'or', ' '];
        $this->operators[Expr\BinaryOp\LogicalXor::class] = [' ', 'xor', ' '];
        $this->operators[Expr\BinaryOp\Minus::class] = [' ', '-', ' '];
        $this->operators[Expr\BinaryOp\Mod::class] = [' ', '%', ' '];
        $this->operators[Expr\BinaryOp\Mul::class] = [' ', '*', ' '];
        $this->operators[Expr\BinaryOp\NotEqual::class] = [' ', '!=', ' '];
        $this->operators[Expr\BinaryOp\NotIdentical::class] = [' ', '!==', ' '];
        $this->operators[Expr\BinaryOp\Plus::class] = [' ', '+', ' '];
        $this->operators[Expr\BinaryOp\Pow::class] = [' ', '**', ' '];
        $this->operators[Expr\BinaryOp\ShiftLeft::class] = [' ', '<<', ' '];
        $this->operators[Expr\BinaryOp\ShiftRight::class] = [' ', '>>', ' '];
        $this->operators[Expr\BinaryOp\Smaller::class] = [' ', '<', ' '];
        $this->operators[Expr\BinaryOp\SmallerOrEqual::class] = [' ', '<=', ' '];
        $this->operators[Expr\BinaryOp\Spaceship::class] = [' ', '<=>', ' '];
        $this->operators[Expr\BitwiseNot::class] = ['', '~', ' '];
        $this->operators[Expr\BooleanNot::class] = ['', '!', ' '];
        $this->operators[Expr\ErrorSuppress::class] = ['', '@', ''];
        $this->operators[Expr\Instanceof_::class] = [' ', 'instanceof', ' '];
        $this->operators[Expr\PostDec::class] = [' ', '--', ''];
        $this->operators[Expr\PostInc::class] = [' ', '++', ''];
        $this->operators[Expr\PreDec::class] = ['', '--', ' '];
        $this->operators[Expr\PreInc::class] = ['', '++', ' '];
        $this->operators[Expr\Print_::class] = ['', 'print', ' '];
        $this->operators[Expr\Ternary::class] = [' ', '?:', ' '];
        $this->operators[Expr\UnaryMinus::class] = ['', '-', ''];
        $this->operators[Expr\UnaryPlus::class] = ['', '+', ''];
        $this->operators[Expr\YieldFrom::class] = ['', 'yield from', ' '];
        $this->setOperators();
    }

    protected function setOperators() : void
    {
    }

    /**
     * @param array<int, null|string|Printable> $list
     */
    public function __invoke(array $list) : string
    {
        if (! $list) {
            return "<?php" . $this->eol;
        }

        $this->indentNum = 0;
        $this->line = new Line(
            $this->eol,
            $this->indentNum,
            $this->indentLen,
            $this->indentTab,
            $this->lineLen,
        );
        $this->lines = [];
        $this->nesting = new Nesting();

        while ($list) {
            $p = array_shift($list);
            $this->s($p ?? '');
        }

        $this->newline();
        $output = '';

        foreach ($this->lines as $line) {
            $line->append($output);
        }

        return $this->finish($output);
    }

    protected function finish(string $code) : string
    {
        return "<?php" . $this->eol . trim($code) . $this->eol;
    }

    protected function newline() : void
    {
        $this->lines[] = $this->line;
        $this->line = new Line(
            $this->eol,
            $this->indentNum,
            $this->indentLen,
            $this->indentTab,
            $this->lineLen,
        );
    }

    protected function indent() : void
    {
        $this->indentNum ++;
        $this->line->indent();
    }

    protected function outdent() : void
    {
        $this->indentNum --;
        $this->line->outdent();
    }

    protected function blockFlow() : void
    {
        $this->blockSame();
    }

    protected function blockNext() : void
    {
        $this->newline();
        $this->line[] = '{';
        $this->newline();
        $this->indent();
    }

    protected function blockSame() : void
    {
        $this->line[] = ' {';
        $this->newline();
        $this->indent();
    }

    protected function blockEnd() : void
    {
        $this->clip();
        $this->newline();
        $this->outdent();
        $this->line[] = '}';
        $this->newline();
        $this->newline();
    }

    protected function clip(callable $when = null, string $append = '') : void
    {
        $this->line[] = new Clip($when, $append);
    }

    protected function split(string $class, string $type) : void
    {
        if (! $this->nesting->in(P\Encapsed::class)) {
            $this->line[] = new Split($this->nesting->level(), $class, $type);
        }
    }

    protected function modifiers(?int $flags) : string
    {
        return implode(
            '',
            [
                $flags & Stmt\Class_::MODIFIER_FINAL ? 'final ' : '',
                $flags & Stmt\Class_::MODIFIER_ABSTRACT ? 'abstract ' : '',
                $flags & Stmt\Class_::MODIFIER_PUBLIC ? 'public ' : '',
                $flags & Stmt\Class_::MODIFIER_PROTECTED ? 'protected ' : '',
                $flags & Stmt\Class_::MODIFIER_PRIVATE ? 'private ' : '',
                $flags & Stmt\Class_::MODIFIER_STATIC ? 'static ' : '',
                $flags & Stmt\Class_::MODIFIER_READONLY ? 'readonly ' : '',
            ],
        );
    }

    protected function maybeNewline(Printable $p) : void
    {
        $this->clip();
        $this->newline();

        if ($p->isFirst() || $p->hasComment() || $p->hasAttribute()) {
            return;
        }

        $this->newline();
    }

    protected function s(string|Printable $p) : void
    {
        if ($p instanceof Printable) {
            $this->sPrintable($p);
            return;
        }

        if ($this->nesting->in(P\Heredoc::class)) {
            $this->sHeredocBody($p);
            return;
        }

        $this->line[] = $p;
    }

    protected function sPrintable(Printable $p) : void
    {
        // first printable in body?
        $p->isFirst($this->atFirstInBody);
        $this->atFirstInBody = false;

        // has comment?
        $p->hasComment($this->hadComment);
        $this->hadComment = false;

        // has attribute?
        $p->hasAttribute($this->hadAttribute);
        $this->hadAttribute = false;

        // add the printable to the code
        $last = (string) strrchr(get_class($p), '\\');
        $method = 's' . trim($last, '\\_');
        $this->{$method}($p);
    }

    protected function sArgs(P\Args $p) : void
    {
        $this->line[] = '(';
        $this->nesting->incr(P\Args::class);

        if ($p->isExpansive()) {
            $this->newline();
            $this->indent();
            return;
        }

        if ($p->count && ! $p->isSingleArray) {
            $this->split(P\Args::class, 'incr');
        }
    }

    protected function sArgSeparator(P\Separator $p) : void
    {
        if ($p->isExpansive() || $p->orig->isExpansive()) {
            $this->line[] = ',';
            $this->newline();
        } else {
            $this->line[] = ', ';
            $this->split(P\Args::class, 'incr');
        }
    }

    protected function sArgsEnd(P\Args $p) : void
    {
        if ($p->isExpansive()) {
            $this->line[] = ',';
            $this->newline();
            $this->outdent();
        } elseif ($p->count && ! $p->isSingleArray) {
            $this->split(P\Args::class, 'last');
        }

        $this->line[] = ')';
        $this->nesting->decr(P\Args::class);
    }

    protected function sArray(P\Array_ $p) : void
    {
        $this->nesting->incr(P\Array_::class);
        $this->line[] = '[';
        $this->atFirstInBody = true;

        if ($p->isExpansive()) {
            $this->newline();
            $this->indent();
        } elseif ($p->count) {
            $this->split(P\Array_::class, 'incr');
        }
    }

    protected function sArraySeparator(P\Separator $p) : void
    {
        if ($p->isExpansive() || $p->orig->isExpansive()) {
            $this->line[] = ',';
            $this->newline();
        } else {
            $this->line[] = ', ';
            $this->split(P\Array_::class, 'incr');
        }
    }

    protected function sArrayEnd(P\Array_ $p) : void
    {
        if ($p->isExpansive()) {
            $this->line[] = ',';
            $this->newline();
            $this->outdent();
        } elseif ($p->count) {
            $this->split(P\Array_::class, 'last');
        }

        $this->line[] = ']';
        $this->nesting->decr(P\Array_::class);
    }

    protected function sArrayDim(P\ArrayDim $p) : void
    {
        $this->nesting->incr(P\Array_::class);
        $this->line[] = '[';
    }

    protected function sArrayDimEnd(P\ArrayDim $p) : void
    {
        $this->line[] = ']';
        $this->nesting->decr(P\Array_::class);
    }

    protected function sArrowFunction(P\ArrowFunction $p) : void
    {
        $this->line[] = $p->static ? 'static fn ' : 'fn ';
    }

    protected function sArrowFunctionBody(P\Body $p) : void
    {
        $this->line[] = ' ';
        $this->nesting->incr(P\ArrowFunction::class);
        $this->split(P\ArrowFunction::class, 'incr');
        $this->line[] = '=> ';
    }

    protected function sArrowFunctionBodyEnd(P\Body $p) : void
    {
        $this->split(P\ArrowFunction::class, 'clip');
        $this->nesting->decr(P\ArrowFunction::class);
    }

    protected function sAs(P\As_ $p) : void
    {
        $this->line[] = ' as ';
    }

    protected function sAttributeGroups(P\AttributeGroups $p) : void
    {
        if ($this->nesting->in(P\Params::class)) {
            return;
        }

        $this->maybeNewline($p);
    }

    protected function sAttributeGroup(P\AttributeGroup $p) : void
    {
        $this->line[] = '#[';
    }

    protected function sAttributeGroupEnd(P\AttributeGroup $p) : void
    {
        $this->line[] = ']';
        $this->newline();
    }

    protected function sAttributeGroupsEnd(P\AttributeGroups $p) : void
    {
        $this->hadAttribute = true;
    }

    protected function sBody(P\Body $p) : void
    {
        $this->atFirstInBody = true;
        $method = 's' . ucfirst($p->type) . 'Body';
        $this->{$method}($p);
    }

    protected function sBodyEnd(P\Body $p) : void
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
        $this->line[] = rtrim('break ' . $p->num) . ';';
        $this->newline();
    }

    protected function sCast(P\Cast $p) : void
    {
        $this->line[] = '(' . $p->type . ') ';
    }

    protected function sClass(P\Class_ $p) : void
    {
        if ($p->name) {
            $this->maybeNewline($p);
        }

        $name = $p->name ? ' ' . $p->name : ' ';
        $this->line[] = $this->modifiers($p->flags) . 'class' . $name;
    }

    protected function sClassBody(P\Body $p) : void
    {
        $this->blockNext();
    }

    protected function sClassBodyEnd(P\Body $p) : void
    {
        $this->blockEnd();
    }

    protected function sClosure(P\Closure $p) : void
    {
        $this->line[] = $p->static ? 'static function ' : 'function ';
    }

    protected function sClosureUse(P\ClosureUse $p) : void
    {
        $this->nesting->incr(P\Params::class);
        $this->line[] = ' use (';

        if ($p->count) {
            $this->split(P\Params::class, 'incr');
        }
    }

    protected function sClosureUseEnd(P\ClosureUse $p) : void
    {
        if ($p->count) {
            $this->split(P\Params::class, 'last');
        }

        $this->line[] = ')';
        $this->nesting->decr(P\Params::class);
    }

    protected function sClosureBody(P\Body $p) : void
    {
        $this->blockSame();
    }

    protected function sClosureBodyEnd(P\Body $p) : void
    {
        $this->clip();
        $this->newline();
        $this->outdent();
        $this->line[] = '}';
    }

    protected function sClosureBodyEmpty(P\BodyEmpty $p) : void
    {
        $this->line[] = ' {}';
    }

    protected function sContinue(P\Continue_ $p) : void
    {
        $this->line[] = rtrim('continue ' . $p->num) . ';';
        $this->newline();
    }

    protected function sComments(P\Comments $p) : void
    {
        $this->clip();
        $this->newline();

        if (! $p->isFirst()) {
            $this->newline();
        }
    }

    protected function sComment(P\Comment $p) : void
    {
        $this->line[] = $p->text;
        $this->newline();
    }

    protected function sCommentsEnd(P\Comments $p) : void
    {
        $this->hadComment = true;
    }

    protected function sCond(P\Cond $p) : void
    {
        $this->nesting->incr(P\Cond::class);
        $this->line[] = '(';

        if ($p->isExpansive()) {
            $this->newline();
            $this->indent();
        } else {
            $this->split(P\Cond::class, 'incr');
        }
    }

    protected function sCondEnd(P\Cond $p) : void
    {
        if ($p->isExpansive()) {
            $this->newline();
            $this->outdent();
        } else {
            $this->split(P\Cond::class, 'same');
        }

        $this->line[] = ')';
        $this->nesting->decr(P\Cond::class);
    }

    protected function sConst(P\Const_ $p) : void
    {
        $this->line[] = 'const ';
    }

    protected function sConstEnd(P\Const_ $p) : void
    {
        $this->line[] = ';';
        $this->newline();
        $this->newline();
    }

    public function sDeclare(P\Declare_ $p) : void
    {
        $this->line[] = 'declare';
    }

    public function sDeclareBody(P\Body $p) : void
    {
        $this->blockSame();
    }

    public function sDeclareBodyEnd(P\Body $p) : void
    {
        $this->blockEnd();
    }

    public function sDeclareBodyEmpty(P\BodyEmpty $p) : void
    {
        $this->line[] = ';';
        $this->newline();
        $this->newline();
    }

    public function sDeclareDirective(P\DeclareDirective $p) : void
    {
        $this->line[] = $p->name . '=';
    }

    protected function sDo(P\Do_ $p) : void
    {
        $this->maybeNewline($p);
        $this->line[] = 'do ';
    }

    protected function sDoBody(P\Body $p) : void
    {
        $this->line[] = '{';
        $this->newline();
        $this->indent();
    }

    protected function sDoBodyEnd(P\Body $p) : void
    {
        $this->clip();
        $this->newline();
        $this->outdent();
        $this->line[] = '} while ';
    }

    protected function sDoEnd(P\Do_ $p) : void
    {
        $this->line[] = ';';
        $this->newline();
        $this->newline();
    }

    protected function sDoubleArrow(P\DoubleArrow $p) : void
    {
        $this->line[] = ' => ';
    }

    protected function sEncapsed(P\Encapsed $p) : void
    {
        $this->nesting->incr(P\Encapsed::class);
    }

    protected function sEncapsedEnd(P\Encapsed $p) : void
    {
        $this->nesting->decr(P\Encapsed::class);
    }

    protected function sEnd(P\End $p) : void
    {
        $method = "s{$p->type}End";
        $this->{$method}($p->orig);
    }

    protected function sEnum(P\Enum_ $p) : void
    {
        $this->maybeNewline($p);
        $this->line[] = 'enum ' . $p->name;
    }

    protected function sEnumBody(P\Body $p) : void
    {
        $this->blockNext();
    }

    protected function sEnumCase(P\EnumCase $p) : void
    {
        $this->maybeNewline($p);
        $this->line[] = 'case ' . $p->name;
    }

    protected function sEnumCaseEnd(P\EnumCase $p) : void
    {
        $this->line[] = ';';
        $this->newline();
    }

    protected function sEnumBodyEnd(P\Body $p) : void
    {
        $this->blockEnd();
    }

    protected function sExpr(P\Expr $p) : void
    {
    }

    protected function sExprEnd(P\Expr $p) : void
    {
        $this->line[] = ';';
        $this->newline();
    }

    protected function sExtends(P\Extends_ $extends) : void
    {
        $this->line[] = ' extends ' . $extends->name;
    }

    protected function sFalse(P\False_ $p) : void
    {
        $this->line[] = 'false';
    }

    protected function sFor(P\For_ $p) : void
    {
        $this->maybeNewline($p);
        $this->line[] = 'for ';
    }

    protected function sForBody(P\Body $p) : void
    {
        $this->blockSame();
    }

    protected function sForBodyEnd(P\Body $p) : void
    {
        $this->blockEnd();
    }

    protected function sForExprSeparator(P\Separator $p) : void
    {
        $this->line[] = '; ';
        $this->split(P\Args::class, 'same');
    }

    protected function sForeach(P\Foreach_ $p) : void
    {
        $this->maybeNewline($p);
        $this->line[] = 'foreach ';
    }

    protected function sForeachBody(P\Body $p) : void
    {
        $this->blockSame();
    }

    protected function sForeachBodyEnd(P\Body $p) : void
    {
        $this->blockEnd();
    }

    protected function sFunction(P\Function_ $p) : void
    {
        $this->maybeNewline($p);
        $this->line[] = $this->modifiers($p->flags) . 'function ';
    }

    protected function sFunctionBodyEmpty(P\BodyEmpty $p) : void
    {
        $this->line[] = ';';
        $this->newline();
    }

    protected function sFunctionBody(P\Body $p) : void
    {
        $this->newline();
        $this->clip(
            when: fn (string $lastLine) : bool => trim($lastLine) === ')',
            append: ' ',
        );
        $this->line[] = '{';
        $this->newline();
        $this->indent();
    }

    protected function sFunctionBodyEnd(P\Body $p) : void
    {
        $this->blockEnd();
    }

    protected function sGoto(P\Goto_ $p) : void
    {
        $this->line[] = "goto {$p->label};";
        $this->newline();
    }

    protected function sHaltCompiler(P\HaltCompiler $p) : void
    {
        $this->line[] = '__halt_compiler();';
    }

    protected function sHeredoc(P\Heredoc $p) : void
    {
        $this->line[] = "<<<{$p->label}";
        $this->nesting->incr(P\Heredoc::class);
        $this->newline();
    }

    protected function sHeredocBody(string $p) : void
    {
        $lines = explode($this->eol, $p);
        $this->line[] = array_shift($lines);

        foreach ($lines as $line) {
            $this->newline();
            $this->line[] = $line;
        }
    }

    protected function sHeredocEnd(P\Heredoc $p) : void
    {
        $this->newline();
        $this->nesting->decr(P\Heredoc::class);
        $this->line[] = $p->label;
    }

    protected function sIf(P\If_ $p) : void
    {
        $this->maybeNewline($p);
        $this->line[] = 'if ';
    }

    protected function sIfBody(P\Body $p) : void
    {
        $this->blockSame();
    }

    protected function sElseIf(P\ElseIf_ $p) : void
    {
        $this->clip();
        $this->newline();
        $this->outdent();
        $this->line[] = '} elseif ';
    }

    protected function sElseIfBody(P\Body $p) : void
    {
        $this->blockSame();
    }

    protected function sElse(P\Else_ $p) : void
    {
        $this->clip();
        $this->newline();
        $this->outdent();
        $this->line[] = '} else ';
    }

    protected function sElseBody(P\Body $p) : void
    {
        $this->line[] = '{';
        $this->newline();
        $this->indent();
    }

    protected function sIfEnd(P\If_ $p) : void
    {
        $this->blockEnd();
    }

    protected function sImplements(P\Implements_ $p) : void
    {
        $this->line[] = ' implements ';
        $this->split(P\Implements_::class, 'incr');
    }

    protected function sImplementsSeparator(P\Separator $p) : void
    {
        $this->line[] = ', ';
        $this->split(P\Implements_::class, 'incr');
    }

    protected function sImplementsEnd(P\Implements_ $p) : void
    {
    }

    protected function sInfix(P\Infix $p) : void
    {
    }

    /**
     * Handles line split for `&&`, `||`, `.`, and `?:`.
     *
     * For `? ... :` ternaries, see sTernary().
     */
    protected function sInfixOp(P\InfixOp $p) : void
    {
        $this->line[] = $this->operators[$p->class][0];

        switch ($p->class) {
            case Expr\BinaryOp\BooleanAnd::class:
            case Expr\BinaryOp\BooleanOr::class:
            case Expr\BinaryOp\LogicalAnd::class:
            case Expr\BinaryOp\LogicalOr::class:
                if ($this->nesting->in(P\Cond::class)) {
                    $this->split($p->class, 'same');
                } else {
                    $this->split($p->class, 'incr');
                }

                break;

            case Expr\BinaryOp\Coalesce::class:
                $this->split($p->class, 'incr');
                break;

            case Expr\BinaryOp\Concat::class:
                $this->nesting->incr(Expr\BinaryOp\Concat::class);
                $this->split($p->class, 'incr');
                break;

            case Expr\Ternary::class:
                $this->nesting->incr(Expr\Ternary::class);
                $this->split($p->class, 'incr');
                break;
        }

        $this->line[] = $this->operators[$p->class][1] . $this->operators[$p->class][2];
    }

    protected function sInfixEnd(P\Infix $p) : void
    {
        switch ($p->class) {
            case Expr\BinaryOp\Coalesce::class:
                $this->split($p->class, 'clip');
                break;

            case Expr\BinaryOp\Concat::class:
                $this->nesting->decr(Expr\BinaryOp\Concat::class);
                break;

            case Expr\Ternary::class:
                $this->nesting->decr(Expr\Ternary::class);
                break;
        }
    }

    protected function sInlineHtml(P\InlineHtml $p) : void
    {
        $this->line[] = '?>' . ($p->newline ? $this->eol : '');
    }

    protected function sInlineHtmlEnd(P\InlineHtml $p) : void
    {
        $this->line[] = '<?php';
        $this->newline();
    }

    protected function sInstanceOp(P\InstanceOp $p) : void
    {
        if (! $p->isFluent()) {
            $this->line[] = $p->str;
            return;
        }

        if ($p->type === 'method' || $p->type === 'property' && $p->fluentNum > 1) {
            $this->nesting->incr(P\MemberOp::class);
            $this->split(P\MemberOp::class, 'incr');
        }

        $this->line[] = $p->str;
    }

    protected function sInstanceOpEnd(P\InstanceOp $p) : void
    {
        if (! $p->isFluent()) {
            return;
        }

        if ($p->type === 'method' || $p->type === 'property' && $p->fluentNum > 1) {
            $this->nesting->decr(P\MemberOp::class);
        }
    }

    protected function sInterface(P\Interface_ $p) : void
    {
        $this->maybeNewline($p);
        $this->line[] = 'interface ' . $p->name;
    }

    protected function sInterfaceBody(P\Body $p) : void
    {
        $this->blockNext();
    }

    protected function sInterfaceBodyEnd(P\Body $p) : void
    {
        $this->blockEnd();
    }

    protected function sLabel(P\Label $p) : void
    {
        $this->newline();
        $this->line[] = "{$p->name}:";
        $this->newline();
    }

    protected function sMatch(P\Match_ $p) : void
    {
        $this->line[] = 'match ';
    }

    protected function sMatchBody(P\Body $p) : void
    {
        $this->blockSame();
    }

    protected function sMatchSeparator(P\Separator $p) : void
    {
        $this->line[] = ', ';
        $this->split(P\Args::class, 'same');
    }

    protected function sMatchArm(P\MatchArm $p) : void
    {
    }

    protected function sMatchArmEnd(P\MatchArm $p) : void
    {
        $this->line[] = ',';
        $this->newline();
    }

    protected function sMatchBodyEnd(P\Body $p) : void
    {
        $this->clip();
        $this->newline();
        $this->outdent();
        $this->line[] = '}';
    }

    protected function sModifiers(P\Modifiers $modifiers) : void
    {
        $this->line[] = $this->modifiers($modifiers->flags);
    }

    protected function sNamespace(P\Namespace_ $p) : void
    {
        $this->line[] = 'namespace';

        if ($p->name) {
            $this->line[] = ' ' . $p->name;
        }
    }

    protected function sNamespaceBody(P\Body $p) : void
    {
        $this->blockNext();
    }

    protected function sNamespaceBodyEnd(P\Body $p) : void
    {
        $this->blockEnd();
    }

    protected function sNamespaceBodyEmpty(P\BodyEmpty $p) : void
    {
        $this->line[] = ';';
        $this->newline();
        $this->newline();
    }

    protected function sNew(P\New_ $p) : void
    {
        $this->line[] = 'new ';
    }

    protected function sNowdoc(P\Nowdoc $p) : void
    {
        $this->line[] = "<<<'{$p->label}'";
        $this->newline();

        foreach (explode($this->eol, $p->value) as $line) {
            $this->line[] = $line;
            $this->newline();
        }

        $this->line[] = $p->label;
    }

    protected function sNull(P\Null_ $p) : void
    {
        $this->line[] = 'null';
    }

    protected function sParamName(P\ParamName $p) : void
    {
        $this->line[] = $p->name . ': ';
    }

    protected function sParams(P\Params $p) : void
    {
        $this->nesting->incr(P\Params::class);
        $this->line[] = '(';

        if ($p->isExpansive()) {
            $this->newline();
            $this->indent();
        } elseif ($p->count) {
            $this->split(P\Params::class, 'incr');
        }
    }

    protected function sParamSeparator(P\Separator $p) : void
    {
        if ($p->isExpansive() || $p->orig->isExpansive()) {
            $this->line[] = ',';
            $this->newline();
        } else {
            $this->line[] = ', ';
            $this->split(P\Params::class, 'incr');
        }
    }

    protected function sParamsEnd(P\Params $p) : void
    {
        if ($p->isExpansive()) {
            $this->line[] = ',';
            $this->newline();
            $this->outdent();
        } elseif ($p->count) {
            $this->split(P\Params::class, 'last');
        }

        $this->line[] = ')';
        $this->nesting->decr(P\Params::class);
    }

    protected function sPostfixOp(P\PostfixOp $p) : void
    {
        $this->line[] = $this->operators[$p->class][0]
            . $this->operators[$p->class][1]
            . $this->operators[$p->class][2];
    }

    protected function sPrecedence(P\Precedence $p) : void
    {
        $this->line[] = '(';
        $this->nesting->incr(P\Precedence::class);
        $this->split(P\Precedence::class, 'incr');
    }

    protected function sPrecedenceEnd(P\Precedence $p) : void
    {
        $this->split(P\Precedence::class, 'same');
        $this->line[] = ')';
        $this->nesting->decr(P\Precedence::class);
    }

    protected function sPrefixOp(P\PrefixOp $p) : void
    {
        $this->line[] = $this->operators[$p->class][0]
            . $this->operators[$p->class][1]
            . $this->operators[$p->class][2];
    }

    protected function sProperty(P\Property $p) : void
    {
        $this->maybeNewline($p);
        $this->line[] = $this->modifiers($p->flags);
    }

    protected function sPropertyEnd(P\Property $end) : void
    {
        $this->line[] = ';';
        $this->newline();
    }

    protected function sReturn(P\Return_ $p) : void
    {
        $this->line[] = $p->isEmpty ? 'return' : 'return ';
    }

    protected function sReturnEnd(P\Return_ $p) : void
    {
        $this->line[] = ';';
        $this->newline();
    }

    protected function sReturnType(P\ReturnType $p) : void
    {
        $this->line[] = ' : ';
    }

    protected function sStaticOp(P\StaticOp $p) : void
    {
        if ($p->isFluent()) {
            $this->nesting->incr(P\MemberOp::class);
        }

        $this->line[] = $p->str;
    }

    protected function sStaticOpEnd(P\StaticOp $p) : void
    {
        if ($p->isFluent()) {
            $this->nesting->decr(P\MemberOp::class);
        }
    }

    protected function sTrue(P\True_ $p) : void
    {
        $this->line[] = 'true';
    }

    protected function sReservedArg(P\ReservedArg $p) : void
    {
        $this->line[] = '(';
    }

    protected function sReservedArgEnd(P\ReservedArg $p) : void
    {
        $this->line[] = ')';
    }

    protected function sReservedStmt(P\ReservedStmt $p) : void
    {
        $this->line[] = $p->name . ' ';
    }

    protected function sReservedStmtEnd(P\ReservedStmt $p) : void
    {
        $this->line[] = ';';
        $this->newline();
    }

    protected function sReservedWord(P\ReservedWord $p) : void
    {
        $this->line[] = $p->name . ' ';
    }

    protected function sSeparator(P\Separator $p) : void
    {
        $method = 's' . ucfirst($p->type) . 'Separator';

        if (method_exists($this, $method)) {
            $this->{$method}($p);
            return;
        }

        $this->line[] = ', ';
    }

    protected function sSwitch(P\Switch_ $p) : void
    {
        $this->maybeNewline($p);
        $this->line[] = 'switch ';
    }

    protected function sSwitchBody(P\Body $p) : void
    {
        $this->blockSame();
    }

    protected function sSwitchCase(P\SwitchCase $p) : void
    {
        $this->line[] = 'case ';
    }

    protected function sSwitchCaseEnd(P\SwitchCase $p) : void
    {
        $this->line[] = ':';

        if ($p->hasBody) {
            $this->newline();
            $this->indent();
        } else {
            $this->newline();
        }
    }

    protected function sSwitchCaseDefault(P\SwitchCaseDefault $p) : void
    {
        $this->line[] = 'default';
    }

    protected function sSwitchCaseDefaultEnd(P\SwitchCaseDefault $p) : void
    {
        $this->line[] = ':';

        if ($p->hasBody) {
            $this->newline();
            $this->indent();
        } else {
            $this->newline();
        }
    }

    protected function sSwitchCaseBody(P\Body $p) : void
    {
    }

    protected function sSwitchCaseBodyEnd(P\Body $p) : void
    {
        $this->clip();
        $this->newline();
        $this->newline();
        $this->outdent();
    }

    protected function sSwitchBodyEnd(P\Body $p) : void
    {
        $this->blockEnd();
    }

    /**
     * Handles `$var ? $true : $false`.
     *
     * @see sInfixOp() for `$var ?: $false` handling.
     */
    protected function sTernary(P\Ternary $p) : void
    {
        $this->line[] = ' ';
        $this->nesting->incr(Expr\Ternary::class);
        $this->split(Expr\Ternary::class, 'incr');
        $this->line[] = $p->operator . ' ';
    }

    protected function sTernaryEnd(P\Ternary $p) : void
    {
        $this->nesting->decr(Expr\Ternary::class);
    }

    protected function sThrow(P\Throw_ $p) : void
    {
        $this->maybeNewline($p);
        $this->line[] = 'throw ';
    }

    protected function sThrowEnd(P\Throw_ $p) : void
    {
        $this->line[] = ';';
        $this->newline();
    }

    protected function sTrait(P\Trait_ $p) : void
    {
        $this->maybeNewline($p);
        $this->line[] = 'trait ' . $p->name;
    }

    protected function sTraitBody(P\Body $p) : void
    {
        $this->blockNext();
    }

    protected function sTraitBodyEnd(P\Body $p) : void
    {
        $this->blockEnd();
    }

    protected function sTry(P\Try_ $p) : void
    {
        $this->maybeNewline($p);
        $this->line[] = 'try';
    }

    protected function sTryBody(P\Body $p) : void
    {
        $this->blockSame();
    }

    protected function sTryCatch(P\TryCatch $p) : void
    {
        $this->clip();
        $this->newline();
        $this->outdent();
        $this->line[] = '} catch ';
    }

    protected function sTryCatchBody(P\Body $p) : void
    {
        $this->blockSame();
    }

    protected function sTryFinally(P\TryFinally $p) : void
    {
        $this->clip();
        $this->newline();
        $this->outdent();
        $this->line[] = '} finally ';
    }

    protected function sTryFinallyBody(P\Body $p) : void
    {
        $this->line[] = '{';
        $this->newline();
        $this->indent();
    }

    protected function sTryEnd(P\Try_ $p) : void
    {
        $this->blockEnd();
    }

    protected function sUnset(P\Unset_ $unset) : void
    {
        $this->line[] = 'unset(';
    }

    protected function sUnsetEnd(P\Unset_ $end) : void
    {
        $this->line[] = ');';
        $this->newline();
    }

    protected function sUseImport(P\UseImport $p) : void
    {
        $this->line[] = 'use ' . ($p->type ? $p->type . ' ' : '');

        if ($p->prefix) {
            $this->line[] = $p->prefix . '\\{';
        }
    }

    protected function sUseImportEnd(P\UseImport $p) : void
    {
        if ($p->prefix) {
            $this->line[] = '}';
        }

        $this->line[] = ';';
        $this->newline();
    }

    protected function sUseTrait(P\UseTrait $p) : void
    {
        $this->line[] = 'use ';
    }

    protected function sUseTraitBody(P\Body $p) : void
    {
        $this->blockSame();
    }

    protected function sUseTraitBodyEnd(P\Body $p) : void
    {
        $this->outdent();
        $this->line[] = '}';
        $this->newline();
    }

    protected function sUseTraitAs(P\UseTraitAs $p) : void
    {
        if ($p->trait) {
            $this->line[] = $p->trait . '::';
        }

        $this->line[] = $p->oldName . ' as';

        if ($p->flags) {
            $this->line[] = ' ' . rtrim($this->modifiers($p->flags), ' ');
        }

        if ($p->newName) {
            $this->line[] = ' ' . $p->newName;
        }

        $this->line[] = ';';
        $this->newline();
    }

    protected function sUseTraitInsteadof(P\UseTraitInsteadof $p) : void
    {
        $this->line[] = $p->trait;
        $this->line[] = '::' . $p->method . ' insteadof ';
    }

    protected function sUseTraitInsteadOfEnd(P\UseTraitInsteadof $p) : void
    {
        $this->line[] = ';';
        $this->newline();
    }

    protected function sUseTraitEnd(P\UseTrait $p) : void
    {
        $this->line[] = ';';
        $this->newline();
    }

    protected function sWhile(P\While_ $p) : void
    {
        $this->maybeNewline($p);
        $this->line[] = 'while ';
    }

    protected function sWhileBody(P\Body $p) : void
    {
        $this->blockSame();
    }

    protected function sWhileBodyEnd(P\Body $p) : void
    {
        $this->blockEnd();
    }

    protected function sYield(P\Yield_ $p) : void
    {
        $this->line[] = $p->isEmpty ? 'yield' : 'yield ';
    }
}
