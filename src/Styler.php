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
    protected int $argsLevel = 0;

    protected int $arrayLevel = 0;

    protected bool $atFirstInBody = false;

    protected Code $code;

    protected int $condLevel = 0;

    protected bool $hadComment = false;

    protected int $memberLevel = 0;

    /**
     * @var array<class-string, string>
     */
    protected array $operator = [
        Expr\Assign::class => '=',
        Expr\AssignOp\BitwiseAnd::class => '&=',
        Expr\AssignOp\BitwiseOr::class => '|=',
        Expr\AssignOp\BitwiseXor::class => '^=',
        Expr\AssignOp\Coalesce::class => '??=',
        Expr\AssignOp\Concat::class => '.=',
        Expr\AssignOp\Div::class => '/=',
        Expr\AssignOp\Minus::class => '-=',
        Expr\AssignOp\Mod::class => '%=',
        Expr\AssignOp\Mul::class => '*=',
        Expr\AssignOp\Plus::class => '+=',
        Expr\AssignOp\Pow::class => '**=',
        Expr\AssignOp\ShiftLeft::class => '<<=',
        Expr\AssignOp\ShiftRight::class => '>>=',
        Expr\AssignRef::class => '= &',
        Expr\BinaryOp\BitwiseAnd::class => '&',
        Expr\BinaryOp\BitwiseOr::class => '|',
        Expr\BinaryOp\BitwiseXor::class => '^',
        Expr\BinaryOp\BooleanAnd::class => '&&',
        Expr\BinaryOp\BooleanOr::class => '||',
        Expr\BinaryOp\Coalesce::class => '??',
        Expr\BinaryOp\Concat::class => '.',
        Expr\BinaryOp\Div::class => '/',
        Expr\BinaryOp\Equal::class => '==',
        Expr\BinaryOp\Greater::class => '>',
        Expr\BinaryOp\GreaterOrEqual::class => '>=',
        Expr\BinaryOp\Identical::class => '===',
        Expr\BinaryOp\LogicalAnd::class => 'and',
        Expr\BinaryOp\LogicalOr::class => 'or',
        Expr\BinaryOp\LogicalXor::class => 'xor',
        Expr\BinaryOp\Minus::class => '-',
        Expr\BinaryOp\Mod::class => '%',
        Expr\BinaryOp\Mul::class => '*',
        Expr\BinaryOp\NotEqual::class => '!=',
        Expr\BinaryOp\NotIdentical::class => '!==',
        Expr\BinaryOp\Plus::class => '+',
        Expr\BinaryOp\Pow::class => '**',
        Expr\BinaryOp\ShiftLeft::class => '<<',
        Expr\BinaryOp\ShiftRight::class => '>>',
        Expr\BinaryOp\Smaller::class => '<',
        Expr\BinaryOp\SmallerOrEqual::class => '<=',
        Expr\BinaryOp\Spaceship::class => '<=>',
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

    /**
     * @param non-empty-string $eol
     * @param string[] $split
     */
    public function __construct(
        protected string $eol = "\n",
        protected int $lineLen = 80,
        protected string $indentStr = "    ",
        protected int $indentLen = 0,
        protected array $split = [
            'concat',
            'array',
            'ternary',
            'cond',
            'bool_and',
            'precedence',
            'bool_or',
            'member_args',
            'coalesce',
            'params',
        ],
    ) {
    }

    /**
     * @param array<int, null|string|Printable> $list
     */
    public function style(array $list) : string
    {
        if (! $list) {
            return "<?php" . $this->eol;
        }

        $this->atFirstInBody = true;
        $this->argsLevel = 0;
        $this->arrayLevel = 0;
        $this->hadComment = false;
        $this->code = $this->newCode();

        while ($list) {
            $p = array_shift($list);
            $this->s($p);
        }

        $this->done();
        $file = "<?php" . $this->eol . ltrim($this->code->getFile());

        return rtrim($file) . $this->eol;
    }

    protected function newCode() : Code
    {
        return new Code(
            $this->eol,
            $this->lineLen,
            $this->indentStr,
            $this->indentLen,
            $this->split,
        );
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

    protected function cuddleParen() : void
    {
        $this->code[] = ['cuddleParen'];
    }

    protected function newline() : void
    {
        $this->code[] = ['newline'];
    }

    protected function forceSplit() : void
    {
        $this->code[] = ['forceSplit'];
    }

    protected function split(
        string $class,
        int $level = null,
        string $type = null,
        mixed ...$args,
    ) : void
    {
        $this->code->split($class, $level, $type, ...$args);
    }

    protected function modifiers(?int $flags) : string
    {
        return ''
            . ($flags & Stmt\Class_::MODIFIER_FINAL ? 'final ' : '')
            . ($flags & Stmt\Class_::MODIFIER_ABSTRACT ? 'abstract ' : '')
            . ($flags & Stmt\Class_::MODIFIER_PUBLIC ? 'public ' : '')
            . ($flags & Stmt\Class_::MODIFIER_PROTECTED ? 'protected ' : '')
            . ($flags & Stmt\Class_::MODIFIER_PRIVATE ? 'private ' : '')
            . ($flags & Stmt\Class_::MODIFIER_STATIC ? 'static ' : '')
            . ($flags & Stmt\Class_::MODIFIER_READONLY ? 'readonly ' : '')
        ;
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
        /** @var string */
        $last = strrchr(get_class($p), '\\');
        $type = trim($last, '\\_');
        $method = 's' . $type;
        $this->{$method}($p);
    }

    protected function sArgs(P\Args $p) : void
    {
        $this->argsLevel ++;
        $this->code[] = '(';

        if ($p->count) {
            $this->split(P\Args::class, $this->argsLevel);
        }
    }

    protected function sArgSeparator(P\Separator $p) : void
    {
        $this->code[] = ', ';
        $this->split(P\Args::class, $this->argsLevel, 'mid');
    }

    protected function sArgsEnd(P\ArgsEnd $p) : void
    {
        if ($p->count) {
            $this->split(P\Args::class, $this->argsLevel, 'end', ',');
        }

        $this->code[] = ')';
        $this->argsLevel --;
    }

    protected function sArray(P\Array_ $p) : void
    {
        $this->arrayLevel ++;
        $this->code[] = '[';
        $this->atFirstInBody = true;

        if ($p->count) {
            $this->split(P\Array_::class, $this->arrayLevel);
        }
    }

    protected function sArraySeparator(P\Separator $p) : void
    {
        $this->code[] = ', ';
        $this->split(P\Array_::class, $this->arrayLevel, 'mid');
    }

    protected function sArrayEnd(P\ArrayEnd $p) : void
    {
        if ($p->count) {
            $this->split(P\Array_::class, $this->arrayLevel, 'end', ',');
        }

        $this->code[] = ']';
        $this->arrayLevel --;
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
        $this->newline();
    }

    protected function sBody(P\Body $p) : void
    {
        $this->atFirstInBody = true;
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
        $this->done();
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

        $name = $p->name ? ' ' . $p->name : '';
        $this->code[] = $this->modifiers($p->flags) . 'class' . $name;
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

    protected function sClosureUse(P\ClosureUse $p) : void
    {
        $this->code[] = ' use (';
    }

    protected function sClosureUseEnd(P\ClosureUseEnd $p) : void
    {
        $this->code[] = ')';
    }

    protected function sClosureBody(P\Body $p) : void
    {
        $this->code[] = ' {';
        $this->indent();
        $this->done();

        if ($this->argsLevel) {
            $this->forceSplit();
        }
    }

    protected function sClosureBodyEnd(P\BodyEnd $p) : void
    {
        $this->outdent();
        $this->cuddle();
        $this->code[] = '}';

        if ($this->argsLevel) {
            $this->forceSplit();
        }
    }

    protected function sContinue(P\Continue_ $p) : void
    {
        $this->code[] = rtrim('continue ' . $p->num) . ';';
        $this->done();
    }

    protected function sComments(P\Comments $p) : void
    {
        $this->cuddle();

        if (! $p->isFirst()) {
            $this->commentNewline();
        }
    }

    protected function sComment(P\Comment $p) : void
    {
        $this->code[] = $p->text;
        $this->hadComment = true;
        $this->commentNewline();
    }

    protected function commentNewline() : void
    {
        if ($this->condLevel || $this->argsLevel || $this->arrayLevel) {
            $this->newline();
        } else {
            $this->done();
        }
    }

    protected function sCond(P\Cond $p) : void
    {
        $this->condLevel ++;
        $this->code[] = '(';
        $this->split(P\Cond::class);
    }

    protected function sCondEnd(P\End $p) : void
    {
        $this->split(P\Cond::class, null, 'end');
        $this->code[] = ')';
        $this->condLevel --;
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
        $this->newline();
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
        $this->split(P\Args::class, $this->argsLevel, 'mid');
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
        $this->code[] = $this->modifiers($p->flags) . 'function ';
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
        $this->cuddleParen();
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

    protected function sInfix(P\Infix $p) : void
    {
    }

    protected function sInfixOp(P\InfixOp $p) : void
    {
        $this->code[] = ' ';

        switch ($p->class) {
            case Expr\BinaryOp\BooleanAnd::class:
            case Expr\BinaryOp\BooleanOr::class:
                if (! $this->condLevel) {
                    $this->split($p->class, null, 'cuddle');
                } else {
                    $this->split($p->class, null, 'mid');
                }

                break;

            case Expr\BinaryOp\Coalesce::class:
            case Expr\BinaryOp\Concat::class:
            case Expr\Ternary::class:
                if (! $this->argsLevel) {
                    $this->split($p->class, null, 'cuddle');
                }

                break;
        }

        $this->code[] = $this->operator[$p->class];

        if ($p->class !== Expr\AssignRef::class) {
            $this->code[] = ' ';
        }
    }

    protected function sInfixEnd(P\InfixEnd $p) : void
    {
        switch ($p->class) {
            case Expr\BinaryOp\BooleanAnd::class:
            case Expr\BinaryOp\BooleanOr::class:
                if (! $this->condLevel) {
                    $this->split($p->class, null, 'endCuddle');
                }

                break;

            case Expr\BinaryOp\Coalesce::class:
            case Expr\BinaryOp\Concat::class:
            case Expr\Ternary::class:
                if (! $this->argsLevel) {
                    $this->split($p->class, null, 'endCuddle');
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

    protected function sMember(P\Member $p) : void
    {
        if ($p->operator === '->' || $p->operator === '?->') {
            $this->memberLevel ++;
            $this->split(P\Member::class, $this->memberLevel, 'cuddle');
        }

        $this->code[] = $p->operator;
    }

    protected function sMemberEnd(P\MemberEnd $p) : void
    {
        if ($p->operator === '->' || $p->operator === '?->') {
            $this->split(P\Member::class, $this->memberLevel, 'endCuddle');
            $this->memberLevel --;
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
        $this->newline();
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
        $this->code[] = $p->name . ': ';
    }

    protected function sParams(P\Params $p) : void
    {
        $this->code[] = '(';

        if ($p->count) {
            $this->split(P\Params::class);
        }
    }

    protected function sParamsEnd(P\ParamsEnd $p) : void
    {
        if ($p->count) {
            $this->split(P\Params::class, null, 'end', ',');
        }

        $this->code[] = ')';
    }

    protected function sParamSeparator(P\Separator $p) : void
    {
        $this->code[] = ', ';
        $this->split(P\Params::class, null, 'mid');
    }

    protected function sPostfixOp(P\PostfixOp $p) : void
    {
        $this->code[] = ' ' . $this->operator[$p->class];
    }

    protected function sPrecedence(P\Precedence $p) : void
    {
        $this->code[] = '(';
        $this->split(P\Precedence::class, null, 'cuddle');
    }

    protected function sPrecedenceEnd(P\End $p) : void
    {
        $this->split(P\Precedence::class, null, 'endCuddle');
        $this->code[] = ')';
    }

    protected function sPrefixOp(P\PrefixOp $p) : void
    {
        $this->code[] = $this->operator[$p->class];

        if (
            $p->class === Expr\ErrorSuppress::class
            || $p->class === Expr\UnaryMinus::class
            || $p->class === Expr\UnaryPlus::class
        ) {
            // no space
            return;
        }

        // space after all other prefix ops
        $this->code[] = ' ';
    }

    protected function sProperty(P\Property $p) : void
    {
        $this->maybeNewline($p);
        $this->code[] = $this->modifiers($p->flags);
    }

    protected function sPropertyEnd(P\End $end) : void
    {
        $this->code[] = ';';
        $this->newline();
        $this->done();
    }

    protected function sReturn(P\Return_ $p) : void
    {
        $this->maybeNewline($p);
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
        $this->code[] = 'case ';
    }

    protected function sSwitchCaseEnd(P\End $p) : void
    {
        $this->code[] = ':';
        $this->done();
    }

    protected function sSwitchCaseDefault(P\SwitchCaseDefault $p) : void
    {
        $this->code[] = 'default:';
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
        $this->code[] = ' ';
        $this->split(Expr\Ternary::class, null, 'cuddle');
        $this->code[] = $p->operator . ' ';
    }

    protected function sTernaryEnd(P\End $p) : void
    {
        $this->split(Expr\Ternary::class, null, 'endCuddle');
    }

    protected function sThrow(P\Throw_ $p) : void
    {
        $this->maybeNewline($p);
        $this->code[] = 'throw ';
    }

    protected function sThrowEnd(P\End $p) : void
    {
        $this->code[] = ';';
        $this->done();
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
        $this->code[] = 'try';
    }

    protected function sTryBody(P\Body $p) : void
    {
        $this->code[] = ' {';
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

    protected function sUseTraitInsteadof(P\UseTraitInsteadof $p) : void
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
