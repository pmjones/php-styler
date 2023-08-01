<?php
declare(strict_types=1);

namespace PhpStyler;

use BadMethodCallException;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\AssignOp;
use PhpParser\Node\Expr\BinaryOp;
use PhpParser\Node\Stmt;
use PhpStyler\Printable as P;
use PhpStyler\Printable\Printable;

class Styler
{
    protected bool $atFirstInBody = false;

    protected Code $code;

    protected bool $hadComment = false;

     protected array $operator = [
        AssignOp\BitwiseAnd::class => '&=',
        AssignOp\BitwiseOr::class => '|=',
        AssignOp\BitwiseXor::class => '^=',
        AssignOp\Coalesce::class => '??=',
        AssignOp\Concat::class => '.=',
        AssignOp\Div::class => '/=',
        AssignOp\Minus::class => '-=',
        AssignOp\Mod::class => '%=',
        AssignOp\Mul::class => '*=',
        AssignOp\Plus::class => '+=',
        AssignOp\Pow::class => '**=',
        AssignOp\ShiftLeft::class => '<<=',
        AssignOp\ShiftRight::class => '>>=',
        BinaryOp\BitwiseAnd::class => '&',
        BinaryOp\BitwiseOr::class => '|',
        BinaryOp\BitwiseXor::class => '^',
        BinaryOp\BooleanAnd::class => '&&',
        BinaryOp\BooleanOr::class => '||',
        BinaryOp\Coalesce::class => '??',
        BinaryOp\Concat::class => '.',
        BinaryOp\Div::class => '/',
        BinaryOp\Equal::class => '==',
        BinaryOp\Greater::class => '>',
        BinaryOp\GreaterOrEqual::class => '>=',
        BinaryOp\Identical::class => '===',
        BinaryOp\LogicalAnd::class => 'and',
        BinaryOp\LogicalOr::class => 'or',
        BinaryOp\LogicalXor::class => 'xor',
        BinaryOp\Minus::class => '-',
        BinaryOp\Mod::class => '%',
        BinaryOp\Mul::class => '*',
        BinaryOp\NotEqual::class => '!=',
        BinaryOp\NotIdentical::class => '!==',
        BinaryOp\Plus::class => '+',
        BinaryOp\Pow::class => '**',
        BinaryOp\ShiftLeft::class => '<<',
        BinaryOp\ShiftRight::class => '>>',
        BinaryOp\Smaller::class => '<',
        BinaryOp\SmallerOrEqual::class => '<=',
        BinaryOp\Spaceship::class => '<=>',
        Expr\Assign::class => '=',
        Expr\AssignRef::class => '= &',
        Expr\BitwiseNot::class => '~',
        Expr\BooleanNot::class => '!',
        Expr\ErrorSuppress::class => '@',
        Expr\Instanceof_::class => 'instanceof',
        Expr\PostDec::class => '--',
        Expr\PostInc::class => '++',
        Expr\PreDec::class => '--',
        Expr\PreInc::class => '++',
        Expr\Print_::class => 'print',
        Expr\Ternary::class => '?:',
        Expr\UnaryMinus::class => '-',
        Expr\UnaryPlus::class => '+',
        Expr\YieldFrom::class => 'yield from',
    ];

    public function __construct(
        protected string $eol = "\n",
        protected int $maxlen = 80
    ) {
    }

    public function style(array $list) : string
    {
        if (! $list) {
            return "<?php" . $this->eol;
        }

        $this->atFirstInBody = true;
        $this->hadComment = false;
        $this->code = new Code($this->eol, $this->maxlen);

        while ($list) {
            $p = array_shift($list);
            $this->s($p);
        }

        $this->done();
        $file = "<?php" . $this->eol . ltrim($this->code->getFile());
        return rtrim($file) . $this->eol;
    }

    protected function done() : void
    {
        $this->code->done();
    }

    protected function indent() : void
    {
        $this->code[] = ['indent'];
    }

    protected function outdent() : void
    {
        $this->code[] = ['outdent'];
    }

    protected function cuddle() : void
    {
        $this->code[] = ['cuddle'];
    }

    protected function newline() : void
    {
        $this->code[] = ['newline'];
    }

    protected function split(string $strategy, string $type = '', ...$args) : void
    {
        $this->code[] = ['split', $strategy, $type, ...$args];
    }

    protected function modifiers(int $flags) : string
    {
        return ''
            . ($flags & Stmt\Class_::MODIFIER_FINAL ? 'final ' : '')
            . ($flags & Stmt\Class_::MODIFIER_ABSTRACT ? 'abstract ' : '')
            . ($flags & Stmt\Class_::MODIFIER_PUBLIC ? 'public ' : '')
            . ($flags & Stmt\Class_::MODIFIER_PROTECTED ? 'protected ' : '')
            . ($flags & Stmt\Class_::MODIFIER_PRIVATE ? 'private ' : '')
            . ($flags & Stmt\Class_::MODIFIER_STATIC ? 'static ' : '')
            . ($flags & Stmt\Class_::MODIFIER_READONLY ? 'readonly ' : '');
    }

    protected function maybeNewline(Printable $p) : void
    {
        if ($p->isFirst() || $p->hasComment()) {
            return;
        }

        $this->cuddle();
        $this->newline();
        $this->done();
    }

    protected function s(null|string|Printable $p) : void
    {
        if ($p === null) {
            return;
        }

        if (is_string($p)) {
            $this->code[] = $p;
            return;
        }

        // first printable in body?
        $p->isFirst($this->atFirstInBody);
        $this->atFirstInBody = false;

        // has comment?
        $p->hasComment($this->hadComment);
        $this->hadComment = false;

        // what method to use?
        $type = trim(strrchr(get_class($p), '\\'), '\\_');
        $method = 's' . $type;
        $this->$method($p);
    }

    protected function sArgs(P\Args $p) : void
    {
        $this->code[] = '(';

        if ($p->count) {
            $this->split(Code::SPLIT_RULE_ARGS);
        }
    }

    protected function sArgsEnd(P\ArgsEnd $p) : void
    {
        if ($p->count) {
            $this->split(Code::SPLIT_RULE_ARGS, 'end', ',');
        }

        $this->code[] = ')';
    }

    protected function sArgSeparator(P\Separator $p) : void
    {
        $this->code[] = ', ';
        $this->split(Code::SPLIT_RULE_ARGS, 'mid', ',');
    }

    protected function sArray(P\Array_ $p) : void
    {
        $this->code[] = '[';

        if ($p->count) {
            $this->split(Code::SPLIT_RULE_ARRAY);
        }
    }

    protected function sArraySeparator(P\Separator $p) : void
    {
        $this->code[] = ', ';
        $this->split(Code::SPLIT_RULE_ARRAY, 'mid', ',');
    }

    protected function sArrayEnd(P\ArrayEnd $p) : void
    {
        if ($p->count) {
            $this->split(Code::SPLIT_RULE_ARRAY, 'end', ',');
        }

        $this->code[] = ']';
    }

    protected function sArrowFunction(P\ArrowFunction $p) : void
    {
        $this->code[] = $p->static ? 'static fn ' : 'fn ';
    }

    protected function sAttributeGroup(P\AttributeGroup $p) : void
    {
        $this->code[] = '#[';
    }

    protected function sAttributeGroupEnd(P\End $p) : void
    {
        $this->code[] = ']';
        $this->done();
    }

    protected function sBody(P\Body $p) : void
    {
        $this->atFirstInBody = true;
        $method = 's' . ucfirst($p->type) . 'Body';
        $this->$method($p);
    }

    protected function sBodyEnd(P\BodyEnd $p) : void
    {
        $method = 's' . ucfirst($p->type) . 'BodyEnd';
        $this->$method($p);
    }

    protected function sBodyEmpty(P\BodyEmpty $p) : void
    {
        $method = 's' . ucfirst($p->type) . 'BodyEmpty';
        $this->$method($p);
    }

    protected function sBreak(P\Break_ $p) : void
    {
        $this->code[] = rtrim('break ' . $p->num) . ';';
        $this->newline();
        $this->done();
    }

    protected function sCast(P\Cast $p) : void
    {
        $this->code[] = '(' . $p->type . ') ';
    }

    protected function sClass(P\Class_ $p) : void
    {
        $this->code[] = 'class' . ($p->name ? ' ' . $p->name : '');
    }

    protected function sClassBody(P\Body $p) : void
    {
        $this->newline();
        $this->code[] = '{';
        $this->indent();
        $this->done();
    }

    protected function sClassBodyEnd(P\BodyEnd $p) : void
    {
        $this->outdent();
        $this->cuddle();
        $this->code[] = '}';
        $this->newline();
        $this->done();
    }

    protected function sClosure(P\Closure $p) : void
    {
        $this->code[] = $p->static ? 'static function ' : 'function ';
    }

    protected function sClosureUse(P\ClosureUse $p)
    {
        $this->code[] = ' use (';
    }

    protected function sClosureUseEnd(P\ClosureUseEnd $p)
    {
        $this->code[] = ')';
    }

    protected function sClosureBody(P\Body $p) : void
    {
        $this->code[] = ' {';
        $this->indent();
        $this->done();
    }

    protected function sClosureBodyEnd(P\BodyEnd $p) : void
    {
        $this->outdent();
        $this->cuddle();
        $this->code[] = '}';
    }

    protected function sContinue(P\Continue_ $p) : void
    {
        $this->code[] = rtrim('continue ' . $p->num) . ';';
        $this->done();
    }

    protected function sComment(P\Comment $p) : void
    {
        $this->code[] = $p->text;
        $this->hadComment = true;
        $this->done();
    }

    protected function sComments(P\Comments $p) : void
    {
        $this->cuddle();

        if (! $p->isFirst()) {
            $this->done();
        }
    }

    protected function sCond(P\Cond $p) : void
    {
        $this->code[] = '(';
        $this->split(Code::SPLIT_RULE_CONDITIONS);
    }

    protected function sCondEnd(P\End $p) : void
    {
        $this->split(Code::SPLIT_RULE_CONDITIONS, 'end');
        $this->code[] = ')';
    }

    protected function sConst(P\Const_ $p) : void
    {
        $this->code[] = 'const ';
    }

    protected function sConstEnd(P\End $p) : void
    {
        $this->code[] = ';';
        $this->newline();
        $this->done();
    }

    public function sDeclare(P\Declare_ $p) : void
    {
        $this->code[] = 'declare';
    }

    public function sDeclareBody(P\Body $p) : void
    {
        $this->code[] = ' {';
        $this->indent();
        $this->done();
    }

    public function sDeclareBodyEnd(P\BodyEnd $p) : void
    {
        $this->outdent();
        $this->cuddle();
        $this->code[] = '}';
        $this->done();
    }

    public function sDeclareBodyEmpty(P\BodyEmpty $p) : void
    {
        $this->code[] = ';';
        $this->done();
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
        $this->done();
    }

    protected function sDoBodyEnd(P\BodyEnd $p) : void
    {
        $this->outdent();
        $this->cuddle();
        $this->code[] = '} while ';
    }

    protected function sDoEnd(P\End $p) : void
    {
        $this->code[] = ';';
        $this->newline();
        $this->done();
    }

    protected function sDoubleArrow(P\DoubleArrow $p) : void
    {
        $this->code[] = ' => ';
    }

    protected function sEnd(P\End $p) : void
    {
        $method = 's' . ucfirst($p->type) . 'End';
        $this->$method($p);
    }

    protected function sEnum(P\Enum_ $p) : void
    {
        $this->code[] = 'enum ' . $p->name;
    }

    protected function sEnumBody(P\Body $p) : void
    {
        $this->newline();
        $this->code[] = '{';
        $this->indent();
        $this->done();
    }

    protected function sEnumCase(P\EnumCase $p) : void
    {
        $this->code[] = 'case ' . $p->name;
    }

    protected function sEnumCaseEnd(P\End $p) : void
    {
        $this->code[] = ';';
        $this->newline();
        $this->done();
    }

    protected function sEnumBodyEnd(P\BodyEnd $p) : void
    {
        $this->outdent();
        $this->cuddle();
        $this->code[] = '}';
        $this->newline();
        $this->done();
    }

    protected function sExprEnd(P\End $p) : void
    {
        $this->code[] = ';';
        $this->done();
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
        $this->done();
    }

    protected function sForBodyEnd(P\BodyEnd $p) : void
    {
        $this->outdent();
        $this->cuddle();
        $this->code[] = '}';
        $this->newline();
        $this->done();
    }

    protected function sForExprSeparator(P\Separator $p) : void
    {
        $this->code[] = '; ';
        $this->split(Code::SPLIT_RULE_ARGS, 'mid', ',');
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
        $this->done();
    }

    protected function sForeachBodyEnd(P\BodyEnd $p) : void
    {
        $this->outdent();
        $this->cuddle();
        $this->code[] = '}';
        $this->newline();
        $this->done();
    }

    protected function sFunction(P\Function_ $p) : void
    {
        $this->code[] = 'function ' ;
    }

    protected function sFunctionBodyEmpty(P\BodyEmpty $p) : void
    {
        $this->code[] = ';';
        $this->newline();
        $this->done();
    }

    protected function sFunctionBody(P\Body $p) : void
    {
        $this->newline();
        $this->code[] = '{';
        $this->indent();
        $this->done();
    }

    protected function sFunctionBodyEnd(P\BodyEnd $p) : void
    {
        $this->outdent();
        $this->cuddle();
        $this->code[] = '}';
        $this->newline();
        $this->done();
    }

    protected function sGoto(P\Goto_ $p) : void
    {
        $this->code[] = "goto {$p->label};";
        $this->done();
    }

    protected function sHaltCompiler(P\HaltCompiler $p) : void
    {
        $this->code[] = '__halt_compiler();';
    }

    protected function sHeredoc(P\Heredoc $p) : void
    {
        $this->code[] = "<<<{$p->label}";
        $this->done();
    }

    protected function sHeredocEnd(P\HeredocEnd $p) : void
    {
        $this->newline();
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
        $this->done();
    }

    protected function sElseIf(P\ElseIf_ $p) : void
    {
        $this->outdent();
        $this->cuddle();
        $this->code[] = '} elseif ';
    }

    protected function sElseIfBody(P\Body $p) : void
    {
        $this->code[] = ' {';
        $this->indent();
        $this->done();
    }

    protected function sElse(P\Else_ $p) : void
    {
        $this->outdent();
        $this->cuddle();
        $this->code[] = '} else ';
    }

    protected function sElseBody(P\Body $p) : void
    {
        $this->code[] = '{';
        $this->indent();
        $this->done();
    }

    protected function sIfEnd(P\End $p) : void
    {
        $this->outdent();
        $this->cuddle();
        $this->code[] = '}';
        $this->newline();
        $this->done();
    }

    protected function sImplements(P\Implements_ $implements) : void
    {
        $this->code[] = ' implements ';
    }

    protected function sInfixOp(P\InfixOp $p) : void
    {
        $this->code[] = ' ';

        if (
            $p->class === BinaryOp\BooleanAnd::class
            || $p->class === BinaryOp\BooleanOr::class
        ) {
            $this->split(Code::SPLIT_RULE_CONDITIONS, 'mid');
        }

        $this->code[] = $this->operator[$p->class];

        if ($p->class !== Expr\AssignRef::class) {
            $this->code[] = ' ';
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
        $this->code[] = 'interface ' . $p->name;
    }

    protected function sInterfaceBody(P\Body $p) : void
    {
        $this->newline();
        $this->code[] = '{';
        $this->indent();
        $this->done();
    }

    protected function sInterfaceBodyEnd(P\BodyEnd $p) : void
    {
        $this->outdent();
        $this->cuddle();
        $this->code[] = '}';
        $this->newline();
        $this->done();
    }

    protected function sLabel(P\Label $p) : void
    {
        $this->newline();
        $this->code[] = "{$p->name}:";
        $this->done();
    }

    protected function sMatch(P\Match_ $p) : void
    {
        $this->code[] = 'match ';
    }

    protected function sMatchBody(P\Body $p) : void
    {
        $this->code[] = ' {';
        $this->indent();
        $this->done();
    }

    protected function sMatchArmEnd(P\End $p) : void
    {
        $this->code[] = ',';
        $this->done();
    }

    protected function sMatchBodyEnd(P\BodyEnd $p) : void
    {
        $this->outdent();
        $this->cuddle();
        $this->code[] = '}';
    }

    protected function sMemberFetch(P\MemberFetch $p)
    {
        $this->code[] = $p->operator;
    }

    protected function sMemberFetchEnd(P\MemberFetchEnd $p)
    {
    }

    protected function sMethodCall(P\MethodCall $p)
    {
        if ($p->operator === '->' || $p->operator === '?->') {
            $this->split(Code::SPLIT_RULE_FLUENT, 'cuddle');
        }

        $this->code[] = $p->operator;
    }

    protected function sMethodCallEnd(P\MethodCallEnd $p)
    {
        if ($p->operator === '->' || $p->operator === '?->') {
            $this->split(Code::SPLIT_RULE_FLUENT, 'endCuddle');
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
        $this->done();
    }

    protected function sNamespaceBodyEnd(P\BodyEnd $p) : void
    {
        $this->outdent();
        $this->cuddle();
        $this->code[] = '}';
        $this->newline();
        $this->done();
    }

    protected function sNamespaceBodyEmpty(P\BodyEmpty $p) : void
    {
        $this->code[] = ';';
        $this->done();
    }

    protected function sNew(P\New_ $p) : void
    {
        $this->code[] = 'new ';
    }

    protected function sNowdoc(P\Nowdoc $p) : void
    {
        $this->code[] = "<<<'{$p->label}'";
        $this->done();
    }

    protected function sNull(P\Null_ $p) : void
    {
        $this->code[] = 'null';
    }

    protected function sParamName(P\ParamName $p) : void
    {
        $this->code[] = $p->str . ': ';
    }

    protected function sParams(P\Params $p) : void
    {
        $this->code[] = '(';

        if ($p->count) {
            $this->split(Code::SPLIT_RULE_PARAMS);
        }
    }

    protected function sParamsEnd(P\ParamsEnd $p) : void
    {
        if ($p->count) {
            $this->split(Code::SPLIT_RULE_PARAMS, 'end', ',');
        }

        $this->code[] = ')';
    }

    protected function sParamSeparator(P\Separator $p) : void
    {
        $this->code[] = ', ';
        $this->split(Code::SPLIT_RULE_PARAMS, 'mid', ',');
    }

    protected function sPostfixOp(P\PostfixOp $p) : void
    {
        $this->code[] = ' ' . $this->operator[$p->class];
    }

    protected function sPrecedence(P\Precedence $p) : void
    {
        $this->code[] = '(';
        $this->split(Code::SPLIT_RULE_CONDITIONS);
    }

    protected function sPrecedenceEnd(P\End $p) : void
    {
        $this->split(Code::SPLIT_RULE_CONDITIONS, 'end');
        $this->code[] = ')';
    }

    protected function sPrefixOp(P\PrefixOp $p) : void
    {
        $this->code[] = $this->operator[$p->class];

        if ($p->class !== Expr\ErrorSuppress::class) {
            $this->code[] = ' ';
        }
    }

    protected function sPropertyEnd(P\End $end) : void
    {
        $this->code[] = ';';
        $this->newline();
        $this->done();
    }

    protected function sReturn(P\Return_ $p) : void
    {
        $this->code[] = $p->isEmpty ? 'return' : 'return ';
    }

    protected function sReturnEnd(P\End $p) : void
    {
        $this->code[] = ';';
        $this->done();
    }

    protected function sReturnType(P\ReturnType $p) : void
    {
        $this->code[] = ' : ';
    }

    protected function sTrue(P\True_ $p) : void
    {
        $this->code[] = 'true';
    }

    protected function sReservedFunc(P\ReservedFunc $p) : void
    {
        $this->code[] = $p->name . '(';
    }

    protected function sReservedFuncEnd(P\End $p) : void
    {
        $this->code[] = ')';
    }

    protected function sReservedStmt(P\ReservedStmt $p) : void
    {
        $this->code[] = $p->name . ' ';
    }

    protected function sReservedStmtEnd(P\End $p) : void
    {
        $this->code[] = ';';
        $this->done();
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
        $this->done();
    }

    protected function sSwitchCase(P\SwitchCase $p) : void
    {
        $this->code[] = ($p->isDefault) ? 'default' : 'case ';
    }

    protected function sSwitchCaseCondEnd(P\End $p) : void
    {
        $this->code[] = ':';
        $this->done();
    }

    protected function sSwitchCaseBody(P\Body $p) : void
    {
        $this->indent();
        $this->cuddle();
    }

    protected function sSwitchCaseBodyEnd(P\BodyEnd $p) : void
    {
        $this->outdent();
        $this->cuddle();
        $this->done();
    }

    protected function sSwitchBodyEnd(P\BodyEnd $p) : void
    {
        $this->outdent();
        $this->cuddle();
        $this->code[] = '}';
        $this->newline();
        $this->done();
    }

    protected function sTernary(P\Ternary $p) : void
    {
        $this->split(Code::SPLIT_RULE_CONDITIONS, 'cuddle');
        $this->code[] = ' ' . $p->str . ' ';
    }

    protected function sTernaryEnd(P\End $p) : void
    {
        $this->split(Code::SPLIT_RULE_CONDITIONS, 'endCuddle');
    }

    protected function sThrow(P\Throw_ $p) : void
    {
        $this->code[] = 'throw ';
    }

    protected function sThrowEnd(P\End $p) : void
    {
        $this->code[] = ';';
        $this->done();
    }

    protected function sTrait(P\Trait_ $p) : void
    {
        $this->code[] = 'trait ' . $p->name;
    }

    protected function sTraitBody(P\Body $p) : void
    {
        $this->newline();
        $this->code[] = '{';
        $this->indent();
        $this->done();
    }

    protected function sTraitBodyEnd(P\BodyEnd $p) : void
    {
        $this->outdent();
        $this->cuddle();
        $this->code[] = '}';
        $this->newline();
        $this->done();
    }

    protected function sTry(P\Try_ $p) : void
    {
        $this->maybeNewline($p);
        $this->code[] = 'try {';
        $this->indent();
        $this->done();
    }

    protected function sTryCatch(P\TryCatch $p) : void
    {
        $this->outdent();
        $this->cuddle();
        $this->code[] = '} catch ';
    }

    protected function sTryCatchBody(P\Body $p) : void
    {
        $this->code[] = ' {';
        $this->indent();
        $this->done();
    }

    protected function sTryFinally(P\TryFinally $p) : void
    {
        $this->outdent();
        $this->cuddle();
        $this->code[] = '} finally ';
    }

    protected function sTryFinallyBody(P\Body $p) : void
    {
        $this->code[] = '{';
        $this->indent();
        $this->done();
    }

    protected function sTryEnd(P\End $p) : void
    {
        $this->outdent();
        $this->cuddle();
        $this->code[] = '}';
        $this->newline();
        $this->done();
    }

    protected function sUnset(P\Unset_ $unset) : void
    {
        $this->code[] = 'unset(';
    }

    protected function sUnsetEnd(P\End $end) : void
    {
        $this->code[] = ');';
        $this->done();
    }

    protected function sUseImport(P\UseImport $p) : void
    {
        $this->code[] = 'use ' . ($p->type ?  $p->type . ' ' : '');

        if ($p->prefix) {
            $this->code[] = $p->prefix . '\\{';
        }
    }

    protected function sUseImportEnd(P\UseImportEnd $p) : void
    {
        if ($p->prefix) {
            $this->code[] = '}';
        };

        $this->code[] = ';';
        $this->done();
    }

    protected function sUseTrait(P\UseTrait $p) : void
    {
        $this->code[] = 'use ';
    }

    protected function sUseTraitBody(P\Body $p) : void
    {
        $this->code[] = ' {';
        $this->indent();
        $this->done();
    }

    protected function sUseTraitBodyEnd(P\BodyEnd $p) : void
    {
        $this->outdent();
        $this->cuddle();
        $this->code[] = '}';
        $this->done();
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
        $this->done();
    }

    protected function sUseTraitInsteadOf(P\UseTraitInsteadOf $p) : void
    {
        $this->code[] = $p->trait;
        $this->code[] = '::' . $p->method . ' insteadof ';
    }

    protected function sUseTraitInsteadOfEnd(P\End $p) : void
    {
        $this->code[] = ';';
        $this->done();
    }

    protected function sUseTraitEnd(P\End $p) : void
    {
        $this->code[] = ';';
        $this->done();
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
        $this->done();
    }

    protected function sWhileBodyEnd(P\BodyEnd $p) : void
    {
        $this->outdent();
        $this->cuddle();
        $this->code[] = '}';
        $this->newline();
        $this->done();
    }

    protected function sYield(P\Yield_ $p) : void
    {
        $this->code[] = $p->isEmpty ? 'yield' : 'yield ';
    }
}
