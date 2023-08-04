<?php
declare(strict_types=1);

namespace PhpStyler;

use Exception;
use PhpParser\Internal\DiffElem;
use PhpParser\Internal\PrintableNewAnonClassNode;
use PhpParser\Internal\TokenStream;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\AssignOp;
use PhpParser\Node\Expr\BinaryOp;
use PhpParser\Node\Expr\Cast;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar;
use PhpParser\Node\Scalar\MagicConst;
use PhpParser\Node\Stmt;
use PhpParser\PrettyPrinterAbstract;
use PhpStyler\Printable as P;
use SplObjectStorage;

class Printer
{
    protected SplObjectStorage $commented;

    protected int $namespaceCount = 0;

    protected $precedenceMap = [
        // [precedence, associativity]
        // where for precedence -1 is %left, 0 is %nonassoc and 1 is %right
        BinaryOp\Pow::class => [0, 1],
        Expr\BitwiseNot::class => [10, 1],
        Expr\PreInc::class => [10, 1],
        Expr\PreDec::class => [10, 1],
        Expr\PostInc::class => [10, -1],
        Expr\PostDec::class => [10, -1],
        Expr\UnaryPlus::class => [10, 1],
        Expr\UnaryMinus::class => [10, 1],
        Cast\Int_::class => [10, 1],
        Cast\Double::class => [10, 1],
        Cast\String_::class => [10, 1],
        Cast\Array_::class => [10, 1],
        Cast\Object_::class => [10, 1],
        Cast\Bool_::class => [10, 1],
        Cast\Unset_::class => [10, 1],
        Expr\ErrorSuppress::class => [10, 1],
        Expr\Instanceof_::class => [20, 0],
        Expr\BooleanNot::class => [30, 1],
        BinaryOp\Mul::class => [40, -1],
        BinaryOp\Div::class => [40, -1],
        BinaryOp\Mod::class => [40, -1],
        BinaryOp\Plus::class => [50, -1],
        BinaryOp\Minus::class => [50, -1],
        BinaryOp\Concat::class => [50, -1],
        BinaryOp\ShiftLeft::class => [60, -1],
        BinaryOp\ShiftRight::class => [60, -1],
        BinaryOp\Smaller::class => [70, 0],
        BinaryOp\SmallerOrEqual::class => [70, 0],
        BinaryOp\Greater::class => [70, 0],
        BinaryOp\GreaterOrEqual::class => [70, 0],
        BinaryOp\Equal::class => [80, 0],
        BinaryOp\NotEqual::class => [80, 0],
        BinaryOp\Identical::class => [80, 0],
        BinaryOp\NotIdentical::class => [80, 0],
        BinaryOp\Spaceship::class => [80, 0],
        BinaryOp\BitwiseAnd::class => [90, -1],
        BinaryOp\BitwiseXor::class => [100, -1],
        BinaryOp\BitwiseOr::class => [110, -1],
        BinaryOp\BooleanAnd::class => [120, -1],
        BinaryOp\BooleanOr::class => [130, -1],
        BinaryOp\Coalesce::class => [140, 1],
        Expr\Ternary::class => [150, 0],

        // parser uses %left for assignments, but they really behave as %right
        Expr\Assign::class => [160, 1],
        Expr\AssignRef::class => [160, 1],
        AssignOp\Plus::class => [160, 1],
        AssignOp\Minus::class => [160, 1],
        AssignOp\Mul::class => [160, 1],
        AssignOp\Div::class => [160, 1],
        AssignOp\Concat::class => [160, 1],
        AssignOp\Mod::class => [160, 1],
        AssignOp\BitwiseAnd::class => [160, 1],
        AssignOp\BitwiseOr::class => [160, 1],
        AssignOp\BitwiseXor::class => [160, 1],
        AssignOp\ShiftLeft::class => [160, 1],
        AssignOp\ShiftRight::class => [160, 1],
        AssignOp\Pow::class => [160, 1],
        AssignOp\Coalesce::class => [160, 1],
        Expr\YieldFrom::class => [165, 1],
        Expr\Print_::class => [168, 1],
        BinaryOp\LogicalAnd::class => [170, -1],
        BinaryOp\LogicalXor::class => [180, -1],
        BinaryOp\LogicalOr::class => [190, -1],
        Expr\Include_::class => [200, -1],
    ];

    protected array $list = [];

    public function printFile(array $nodes, Styler $styler) : string
    {
        $this->list = [];
        $this->indentLevel = 0;
        $this->commented = new SplObjectStorage();
        $this->namespaceCount = 0;

        foreach ($nodes as $node) {
            if ($node instanceof Stmt\Namespace_) {
                $this->namespaceCount ++;
            }
        }

        $this->p($nodes);
        return $styler->style($this->list);
    }

    protected function p(null|array|Node $spec) : void
    {
        if (is_array($spec)) {
            foreach ($spec as $sub) {
                $this->p($sub);
            }

            return;
        }

        if ($spec === null) {
            return;
        }

        $this->pComments($spec);

        if ($spec instanceof Stmt\Nop) {
            return;
        }

        $method = 'p' . $spec->getType();
        $this->{$method}($spec);
    }

    protected function pArg(Node\Arg $node) : void
    {
        if ($node->name) {
            $this->list[] = new P\ParamName($node->name->toString());
        }

        $this->pByref($node);
        $this->pUnpack($node);
        $this->p($node->value);
    }

    protected function pArgs(Node $node)
    {
        $this->list[] = new P\Args(count($node->args));
        $this->pSeparate('arg', $node->args);
        $this->list[] = new P\ArgsEnd(count($node->args));
    }

    protected function pAttribute(Node\Attribute $node) : void
    {
        $this->p($node->name);

        if ($node->args) {
            $this->pArgs($node);
        }
    }

    protected function pAttributeGroup(Node\AttributeGroup $node) : void
    {
        $this->list[] = new P\AttributeGroup();
        $this->pSeparate('attribute', $node->attrs);
        $this->pEnd('attributeGroup');
    }

    protected function pAttributeGroups(Node $node) : void
    {
        foreach ($node->attrGroups as $attrGroup) {
            $this->p($attrGroup);
        }
    }

    protected function pBody(string $type) : void
    {
        $this->list[] = new P\Body($type);
    }

    protected function pBodyEnd(string $type) : void
    {
        $this->list[] = new P\BodyEnd($type);
    }

    protected function pBodyEmpty(string $type) : void
    {
        $this->list[] = new P\BodyEmpty($type);
    }

    protected function pByRef(Node $node) : void
    {
        if ($node->byRef) {
            $this->list[] = '&';
        }
    }

    protected function pCast(string $type, Node $node) : void
    {
        $class = get_class($node);
        list($precedence, $associativity) = $this->precedenceMap[$class];
        $this->list[] = new P\Cast($type);
        $this->pPrec($node->expr, $precedence, $associativity, 1);
    }

    protected function pCallLhs(Node $node) : void
    {
        if (! $this->callLhsRequiresParens($node)) {
            $this->p($node);
            return;
        }

        $this->list[] = '(';
        $this->p($node);
        $this->list[] = ')';
    }

    protected function pComments(Node $node) : void
    {
        $commentNodes = $node->getComments();

        if (! $commentNodes) {
            return;
        }

        if ($this->commented->contains($commentNodes[0])) {
            // if you've seen the first comment node from this array,
            // you've seen all the comment nodes from this array
            return;
        }

        $this->commented->attach($commentNodes[0]);
        $this->list[] = new P\Comments(count($commentNodes));

        foreach ($commentNodes as $commentNode) {
            $lines = explode("\n", $commentNode->getReformattedText());

            foreach ($lines as $line) {
                $this->list[] = new P\Comment($line);
            }
        }
    }

    protected function pCond(Node $node) : void
    {
        $this->list[] = new P\Cond();
        $this->p($node->cond);
        $this->pEnd('cond');
    }

    protected function pConst(Node\Const_ $node) : void
    {
        $this->list[] = $this->name($node->name);
        $this->list[] = ' = ';
        $this->p($node->value);
    }

    protected function pDefaultValue(Node $node) : void
    {
        if ($node->default) {
            $this->list[] = ' = ';
            $this->p($node->default);
        }
    }

    protected function pDoubleArrow() : void
    {
        $this->list[] = new P\DoubleArrow();
    }

    protected function pDereferenceLhs(Node $node) : void
    {
        if (! $this->dereferenceLhsRequiresParens($node)) {
            $this->p($node);
            return;
        }

        $this->list[] = '(';
        $this->p($node);
        $this->list[] = ')';
    }

    protected function pEmbrace(Node|string $spec) : void
    {
        if (is_string($spec)) {
            $this->list[] = '$' . $spec;
        } else {
            $this->list[] = '{';
            $this->p($spec);
            $this->list[] = '}';
        }
    }

    protected function pEncapsList(array $encapsList, $quote) : void
    {
        foreach ($encapsList as $element) {
            if ($element instanceof Scalar\EncapsedStringPart) {
                $this->list[] = $this->escapeString($element->value, $quote);
            } else {
                $this->pEmbrace($element);
            }
        }
    }

    protected function pEnd(string $type, string $str = '')
    {
        $this->list[] = new P\End($type, $str);
    }

    protected function pExpr_Array(Expr\Array_ $node) : void
    {
        $this->list[] = new P\Array_(count($node->items));
        $this->pSeparate('array', $node->items);
        $this->list[] = new P\ArrayEnd(count($node->items));
    }

    protected function pExpr_ArrayDimFetch(Expr\ArrayDimFetch $node) : void
    {
        $this->pDereferenceLhs($node->var);
        $this->list[] = '[';
        $this->p($node->dim);
        $this->list[] = ']';
    }

    protected function pExpr_ArrayItem(Expr\ArrayItem $node) : void
    {
        if ($node->key) {
            $this->p($node->key);
            $this->pDoubleArrow();
        }

        $this->pByRef($node);
        $this->pUnpack($node);
        $this->p($node->value);
    }

    protected function pExpr_ArrowFunction(Expr\ArrowFunction $node) : void
    {
        $this->pAttributeGroups($node);
        $this->list[] = new P\ArrowFunction($node->static);
        $this->pByRef($node);
        $this->pParams($node);
        $this->pReturnType($node);
        $this->pDoubleArrow();
        $this->p($node->expr);
    }

    protected function pExpr_Assign(Expr\Assign $node) : void
    {
        $this->pInfixOp(Expr\Assign::class, $node->var, $node->expr);
    }

    protected function pExpr_AssignRef(Expr\AssignRef $node) : void
    {
        $this->pInfixOp(Expr\AssignRef::class, $node->var, $node->expr);
    }

    protected function pExpr_AssignOp_Plus(AssignOp\Plus $node) : void
    {
        $this->pInfixOp(AssignOp\Plus::class, $node->var, $node->expr);
    }

    protected function pExpr_AssignOp_Minus(AssignOp\Minus $node) : void
    {
        $this->pInfixOp(AssignOp\Minus::class, $node->var, $node->expr);
    }

    protected function pExpr_AssignOp_Mul(AssignOp\Mul $node) : void
    {
        $this->pInfixOp(AssignOp\Mul::class, $node->var, $node->expr);
    }

    protected function pExpr_AssignOp_Div(AssignOp\Div $node) : void
    {
        $this->pInfixOp(AssignOp\Div::class, $node->var, $node->expr);
    }

    protected function pExpr_AssignOp_Concat(AssignOp\Concat $node) : void
    {
        $this->pInfixOp(AssignOp\Concat::class, $node->var, $node->expr);
    }

    protected function pExpr_AssignOp_Mod(AssignOp\Mod $node) : void
    {
        $this->pInfixOp(AssignOp\Mod::class, $node->var, $node->expr);
    }

    protected function pExpr_AssignOp_BitwiseAnd(
        AssignOp\BitwiseAnd $node,
    ) : void
    {
        $this->pInfixOp(AssignOp\BitwiseAnd::class, $node->var, $node->expr);
    }

    protected function pExpr_AssignOp_BitwiseOr(AssignOp\BitwiseOr $node) : void
    {
        $this->pInfixOp(AssignOp\BitwiseOr::class, $node->var, $node->expr);
    }

    protected function pExpr_AssignOp_BitwiseXor(
        AssignOp\BitwiseXor $node,
    ) : void
    {
        $this->pInfixOp(AssignOp\BitwiseXor::class, $node->var, $node->expr);
    }

    protected function pExpr_AssignOp_ShiftLeft(AssignOp\ShiftLeft $node) : void
    {
        $this->pInfixOp(AssignOp\ShiftLeft::class, $node->var, $node->expr);
    }

    protected function pExpr_AssignOp_ShiftRight(
        AssignOp\ShiftRight $node,
    ) : void
    {
        $this->pInfixOp(AssignOp\ShiftRight::class, $node->var, $node->expr);
    }

    protected function pExpr_AssignOp_Pow(AssignOp\Pow $node) : void
    {
        $this->pInfixOp(AssignOp\Pow::class, $node->var, $node->expr);
    }

    protected function pExpr_AssignOp_Coalesce(AssignOp\Coalesce $node) : void
    {
        $this->pInfixOp(AssignOp\Coalesce::class, $node->var, $node->expr);
    }

    protected function pExpr_BinaryOp_Plus(BinaryOp\Plus $node) : void
    {
        $this->pInfixOp(BinaryOp\Plus::class, $node->left, $node->right);
    }

    protected function pExpr_BinaryOp_Minus(BinaryOp\Minus $node) : void
    {
        $this->pInfixOp(BinaryOp\Minus::class, $node->left, $node->right);
    }

    protected function pExpr_BinaryOp_Mul(BinaryOp\Mul $node) : void
    {
        $this->pInfixOp(BinaryOp\Mul::class, $node->left, $node->right);
    }

    protected function pExpr_BinaryOp_Div(BinaryOp\Div $node) : void
    {
        $this->pInfixOp(BinaryOp\Div::class, $node->left, $node->right);
    }

    protected function pExpr_BinaryOp_Concat(BinaryOp\Concat $node) : void
    {
        $this->pInfixOp(BinaryOp\Concat::class, $node->left, $node->right);
    }

    protected function pExpr_BinaryOp_Mod(BinaryOp\Mod $node) : void
    {
        $this->pInfixOp(BinaryOp\Mod::class, $node->left, $node->right);
    }

    protected function pExpr_BinaryOp_BooleanAnd(
        BinaryOp\BooleanAnd $node,
    ) : void
    {
        $this->pInfixOp(BinaryOp\BooleanAnd::class, $node->left, $node->right);
    }

    protected function pExpr_BinaryOp_BooleanOr(BinaryOp\BooleanOr $node) : void
    {
        $this->pInfixOp(BinaryOp\BooleanOr::class, $node->left, $node->right);
    }

    protected function pExpr_BinaryOp_BitwiseAnd(
        BinaryOp\BitwiseAnd $node,
    ) : void
    {
        $this->pInfixOp(BinaryOp\BitwiseAnd::class, $node->left, $node->right);
    }

    protected function pExpr_BinaryOp_BitwiseOr(BinaryOp\BitwiseOr $node) : void
    {
        $this->pInfixOp(BinaryOp\BitwiseOr::class, $node->left, $node->right);
    }

    protected function pExpr_BinaryOp_BitwiseXor(
        BinaryOp\BitwiseXor $node,
    ) : void
    {
        $this->pInfixOp(BinaryOp\BitwiseXor::class, $node->left, $node->right);
    }

    protected function pExpr_BinaryOp_ShiftLeft(BinaryOp\ShiftLeft $node) : void
    {
        $this->pInfixOp(BinaryOp\ShiftLeft::class, $node->left, $node->right);
    }

    protected function pExpr_BinaryOp_ShiftRight(
        BinaryOp\ShiftRight $node,
    ) : void
    {
        $this->pInfixOp(BinaryOp\ShiftRight::class, $node->left, $node->right);
    }

    protected function pExpr_BinaryOp_Pow(BinaryOp\Pow $node) : void
    {
        $this->pInfixOp(BinaryOp\Pow::class, $node->left, $node->right);
    }

    protected function pExpr_BinaryOp_LogicalAnd(
        BinaryOp\LogicalAnd $node,
    ) : void
    {
        $this->pInfixOp(BinaryOp\LogicalAnd::class, $node->left, $node->right);
    }

    protected function pExpr_BinaryOp_LogicalOr(BinaryOp\LogicalOr $node) : void
    {
        $this->pInfixOp(BinaryOp\LogicalOr::class, $node->left, $node->right);
    }

    protected function pExpr_BinaryOp_LogicalXor(
        BinaryOp\LogicalXor $node,
    ) : void
    {
        $this->pInfixOp(BinaryOp\LogicalXor::class, $node->left, $node->right);
    }

    protected function pExpr_BinaryOp_Equal(BinaryOp\Equal $node) : void
    {
        $this->pInfixOp(BinaryOp\Equal::class, $node->left, $node->right);
    }

    protected function pExpr_BinaryOp_NotEqual(BinaryOp\NotEqual $node) : void
    {
        $this->pInfixOp(BinaryOp\NotEqual::class, $node->left, $node->right);
    }

    protected function pExpr_BinaryOp_Identical(BinaryOp\Identical $node) : void
    {
        $this->pInfixOp(BinaryOp\Identical::class, $node->left, $node->right);
    }

    protected function pExpr_BinaryOp_NotIdentical(
        BinaryOp\NotIdentical $node,
    ) : void
    {
        $this
            ->pInfixOp(BinaryOp\NotIdentical::class, $node->left, $node->right)
        ;
    }

    protected function pExpr_BinaryOp_Spaceship(BinaryOp\Spaceship $node) : void
    {
        $this->pInfixOp(BinaryOp\Spaceship::class, $node->left, $node->right);
    }

    protected function pExpr_BinaryOp_Greater(BinaryOp\Greater $node) : void
    {
        $this->pInfixOp(BinaryOp\Greater::class, $node->left, $node->right);
    }

    protected function pExpr_BinaryOp_GreaterOrEqual(
        BinaryOp\GreaterOrEqual $node,
    ) : void
    {
        $this
            ->pInfixOp(
                BinaryOp\GreaterOrEqual::class,
                $node->left,
                $node->right,
            )
        ;
    }

    protected function pExpr_BinaryOp_Smaller(BinaryOp\Smaller $node) : void
    {
        $this->pInfixOp(BinaryOp\Smaller::class, $node->left, $node->right);
    }

    protected function pExpr_BinaryOp_SmallerOrEqual(
        BinaryOp\SmallerOrEqual $node,
    ) : void
    {
        $this
            ->pInfixOp(
                BinaryOp\SmallerOrEqual::class,
                $node->left,
                $node->right,
            )
        ;
    }

    protected function pExpr_BinaryOp_Coalesce(BinaryOp\Coalesce $node) : void
    {
        $this->pInfixOp(BinaryOp\Coalesce::class, $node->left, $node->right);
    }

    protected function pExpr_BitwiseNot(Expr\BitwiseNot $node) : void
    {
        $this->pPrefixOp(Expr\BitwiseNot::class, $node->expr);
    }

    protected function pExpr_BooleanNot(Expr\BooleanNot $node) : void
    {
        $this->pPrefixOp(Expr\BooleanNot::class, $node->expr);
    }

    protected function pExpr_Cast_Bool(Cast\Bool_ $node) : void
    {
        $this->pCast('bool', $node);
    }

    protected function pExpr_Cast_Array(Cast\Array_ $node) : void
    {
        $this->pCast('array', $node);
    }

    protected function pExpr_Cast_Double(Cast\Double $node) : void
    {
        $this->pCast('float', $node);
    }

    protected function pExpr_Cast_Int(Cast\Int_ $node) : void
    {
        $this->pCast('int', $node);
    }

    protected function pExpr_Cast_String(Cast\String_ $node) : void
    {
        $this->pCast('string', $node);
    }

    protected function pExpr_Cast_Object(Cast\Object_ $node) : void
    {
        $this->pCast('object', $node);
    }

    protected function pExpr_ClassConstFetch(Expr\ClassConstFetch $node) : void
    {
        $this->pDereferenceLhs($node->class);
        $this->list[] = new P\MemberFetch('::');
        $this->p($node->name);
        $this->list[] = new P\MemberFetchEnd('->');
    }

    protected function pExpr_Clone(Expr\Clone_ $node) : void
    {
        $this->list[] = new P\ReservedWord('clone');
        $this->p($node->expr);
    }

    protected function pExpr_Closure(Expr\Closure $node) : void
    {
        $this->pAttributeGroups($node);
        $this->list[] = new P\Closure($node->static);
        $this->pByRef($node);
        $this->pParams($node);

        if ($node->uses) {
            $this->list[] = new P\ClosureUse();
            $this->pSeparate('arg', $node->uses);
            $this->list[] = new P\ClosureUseEnd();
        }

        $this->pReturnType($node);
        $this->pBody('closure');
        $this->p($node->stmts);
        $this->pBodyEnd('closure');
    }

    protected function pExpr_ClosureUse(Expr\ClosureUse $node) : void
    {
        $this->pByRef($node);
        $this->p($node->var);
    }

    protected function pExpr_ConstFetch(Expr\ConstFetch $node) : void
    {
        $name = $this->name($node->name);

        switch (strtolower($name)) {
            case 'true':
                $this->list[] = new P\True_();
                break;

            case 'false':
                $this->list[] = new P\False_();
                break;

            case 'null':
                $this->list[] = new P\Null_();
                break;

            default:
                $this->list[] = $name;
        }
    }

    protected function pExpr_Empty(Expr\Empty_ $node) : void
    {
        $this->list[] = new P\ReservedFunc('empty');
        $this->p($node->expr);
        $this->pEnd('reservedFunc');
    }

    protected function pExpr_Exit(Expr\Exit_ $node) : void
    {
        $kind = $node->getAttribute('kind', Expr\Exit_::KIND_DIE);
        $word = $kind === Expr\Exit_::KIND_EXIT ? 'exit' : 'die';
        $this->list[] = new P\ReservedFunc($word);
        $this->p($node->expr);
        $this->pEnd('reservedFunc');
    }

    protected function pExpr_Error(Expr\Error $node) : void
    {
        throw new LogicException('Cannot pretty-print AST with Error nodes');
    }

    protected function pExpr_ErrorSuppress(Expr\ErrorSuppress $node) : void
    {
        $this->pPrefixOp(Expr\ErrorSuppress::class, $node->expr);
    }

    protected function pExpr_Eval(Expr\Eval_ $node) : void
    {
        $this->list[] = new P\ReservedFunc('eval');
        $this->p($node->expr);
        $this->pEnd('reservedFunc');
    }

    protected function pExpr_FuncCall(Expr\FuncCall $node) : void
    {
        $this->pCallLhs($node->name);
        $this->pArgs($node);
    }

    protected function pExpr_Include(Expr\Include_ $node) : void
    {
        static $map = [
            Expr\Include_::TYPE_INCLUDE => 'include',
            Expr\Include_::TYPE_INCLUDE_ONCE => 'include_once',
            Expr\Include_::TYPE_REQUIRE => 'require',
            Expr\Include_::TYPE_REQUIRE_ONCE => 'require_once',
        ];
        $this->list[] = new P\ReservedWord($map[$node->type]);
        $this->p($node->expr);
    }

    protected function pExpr_Instanceof(Expr\Instanceof_ $node) : void
    {
        list($precedence,
        $associativity) = $this->precedenceMap[Expr\Instanceof_::class];
        $this->pPrec($node->expr, $precedence, $associativity, -1);
        $this->list[] = new P\InfixOp(Expr\Instanceof_::class);
        $this->pNewVariable($node->class);
    }

    protected function pExpr_Isset(Expr\Isset_ $node) : void
    {
        $this->list[] = new P\ReservedFunc('isset');
        $this->pSeparate('arg', $node->vars);
        $this->pEnd('reservedFunc');
    }

    protected function pExpr_List(Expr\List_ $node) : void
    {
        $this->list[] = new P\ReservedFunc('list');
        $this->pSeparate('arg', $node->items);
        $this->pEnd('reservedFunc');
    }

    protected function pExpr_Match(Expr\Match_ $node) : void
    {
        $this->list[] = new P\Match_();
        $this->pCond($node);
        $this->list[] = new P\Body('match');
        $this->p($node->arms);
        $this->list[] = new P\BodyEnd('match');
    }

    protected function pExpr_MethodCall(Expr\MethodCall $node) : void
    {
        $this->pDereferenceLhs($node->var);
        $this->list[] = new P\MethodCall('->');
        $this->pObjectProperty($node->name);
        $this->pArgs($node);
        $this->list[] = new P\MethodCallEnd('->');
    }

    protected function pExpr_New(Expr\New_ $node)
    {
        $this->list[] = new P\New_();

        if ($node->class instanceof Stmt\Class_) {
            $this->pNewAnonymous($node->class, $node->args);
        } else {
            $this->pNewVariable($node->class);
            $this->pArgs($node);
        }
    }

    protected function pExpr_NullsafeMethodCall(
        Expr\NullsafeMethodCall $node,
    ) : void
    {
        $this->pDereferenceLhs($node->var);
        $this->list[] = new P\MethodCall('?->');
        $this->pObjectProperty($node->name);
        $this->pArgs($node);
        $this->list[] = new P\MethodCallEnd('?->');
    }

    protected function pExpr_NullsafePropertyFetch(
        Expr\NullsafePropertyFetch $node,
    ) : void
    {
        $this->pDereferenceLhs($node->var);
        $this->list[] = new P\MemberFetch('?->');
        $this->pObjectProperty($node->name);
        $this->list[] = new P\MemberFetchEnd('?->');
    }

    protected function pExpr_PropertyFetch(Expr\PropertyFetch $node) : void
    {
        $this->pDereferenceLhs($node->var);
        $this->list[] = new P\MemberFetch('->');
        $this->pObjectProperty($node->name);
        $this->list[] = new P\MemberFetchEnd('->');
    }

    protected function pExpr_PostInc(Expr\PostInc $node) : void
    {
        $this->pPostfixOp(Expr\PostInc::class, $node->var);
    }

    protected function pExpr_PostDec(Expr\PostDec $node) : void
    {
        $this->pPostfixOp(Expr\PostDec::class, $node->var);
    }

    protected function pExpr_PreInc(Expr\PreInc $node) : void
    {
        $this->pPrefixOp(Expr\PreInc::class, $node->var);
    }

    protected function pExpr_PreDec(Expr\PreDec $node) : void
    {
        $this->pPrefixOp(Expr\PreDec::class, $node->var);
    }

    protected function pExpr_Print(Expr\Print_ $node) : void
    {
        $this->pPrefixOp(Expr\Print_::class, $node->expr);
    }

    protected function pExpr_ShellExec(Expr\ShellExec $node) : void
    {
        $this->list[] = '`';
        $this->pEncapsList($node->parts, '`');
        $this->list[] = '`';
    }

    protected function pExpr_StaticPropertyFetch(
        Expr\StaticPropertyFetch $node,
    ) : void
    {
        $this->pDereferenceLhs($node->class);
        $this->list[] = new P\MemberFetch('::$');
        $this->pObjectProperty($node->name);
        $this->list[] = new P\MemberFetchEnd('::$');
    }

    protected function pExpr_StaticCall(Expr\StaticCall $node) : void
    {
        $this->pDereferenceLhs($node->class);
        $this->list[] = new P\MethodCall('::');

        if ($node->name instanceof Expr) {
            if ($node->name instanceof Expr\Variable) {
                $this->p($node->name);
            } else {
                $this->pEmbrace($node->name);
            }
        } else {
            $this->p($node->name);
        }

        $this->pArgs($node);
        $this->list[] = new P\MethodCallEnd('::');
    }

    protected function pExpr_Ternary(Expr\Ternary $node)
    {
        if (! $node->if) {
            $this->pInfixOp(Expr\Ternary::class, $node->cond, $node->else);
            return;
        }

        // lifted from nInfixOp
        list($precedence,
        $associativity) = $this->precedenceMap[Expr\Ternary::class];
        $this->pPrec($node->cond, $precedence, $associativity, -1);
        $this->list[] = new P\Ternary('?');
        $this->p($node->if);
        $this->list[] = new P\End('ternary');
        $this->list[] = new P\Ternary(':');
        $this->pPrec($node->else, $precedence, $associativity, 1);
        $this->list[] = new P\End('ternary');
    }

    protected function pExpr_Throw(Expr\Throw_ $node) : void
    {
        $this->list[] = new P\ReservedWord('throw');
        $this->p($node->expr);
    }

    protected function pExpr_UnaryMinus(Expr\UnaryMinus $node) : void
    {
        if (
            $node->expr instanceof Expr\UnaryMinus
            || $node->expr instanceof Expr\PreDec
        ) {
            // Enforce -(-$expr) instead of --$expr
            $this->list[] = '-(';
            $this->p($node->expr);
            $this->list[] = ')';
            return;
        }

        $this->pPrefixOp(Expr\UnaryMinus::class, $node->expr);
    }

    protected function pExpr_UnaryPlus(Expr\UnaryPlus $node) : void
    {
        if (
            $node->expr instanceof Expr\UnaryPlus
            || $node->expr instanceof Expr\PreInc
        ) {
            // Enforce +(+$expr) instead of ++$expr
            $this->list[] = '+(';
            $this->p($node->expr);
            $this->list[] = ')';
            return;
        }

        $this->pPrefixOp(Expr\UnaryPlus::class, $node->expr);
    }

    protected function pExpr_Variable(Expr\Variable $node) : void
    {
        if ($node->name instanceof Expr) {
            $this->list[] = '${';
            $this->p($node->name);
            $this->list[] = '}';
        } else {
            $this->list[] = '$' . $node->name;
        }
    }

    protected function pExpr_Yield(Expr\Yield_ $node) : void
    {
        $this->list[] = new P\Yield_(! $node->value);

        if ($node->key) {
            $this->p($node->key);
            $this->pDoubleArrow();
        }

        $this->p($node->value);
    }

    protected function pExpr_YieldFrom(Expr\YieldFrom $node) : void
    {
        $this->pPrefixOp(Expr\YieldFrom::class, $node->expr);
    }

    protected function pExtends(Node $node)
    {
        $extends = $node->extends;

        if (! $extends) {
            return;
        }

        if (! is_array($extends)) {
            $this->list[] = new P\Extends_($this->name($node->extends));
            return;
        }

        $names = [];

        foreach ($node->extends as $name) {
            $names[] = $this->name($name);
        }

        $this->list[] = new P\Extends_(implode(', ', $names));
    }

    protected function pIdentifier(Node\Identifier $node) : void
    {
        $this->list[] = $this->name($node);
    }

    protected function pImplements(Node $node) : void
    {
        if ($node->implements) {
            $this->list[] = new P\Implements_();
            $this->pSeparate('implements', $node->implements);
        }
    }

    protected function pInfixOp(
        string $class,
        Node $leftNode,
        Node $rightNode,
    ) : void
    {
        list($precedence, $associativity) = $this->precedenceMap[$class];
        $this->list[] = new P\Infix($class);
        $this->pPrec($leftNode, $precedence, $associativity, -1);
        $this->list[] = new P\InfixOp($class);
        $this->pPrec($rightNode, $precedence, $associativity, 1);
        $this->list[] = new P\InfixEnd($class);
    }

    protected function pMatchArm(Node\MatchArm $node) : void
    {
        if ($node->conds) {
            $this->pSeparate('match', $node->conds);
        } else {
            $this->list[] = 'default';
        }

        $this->pDoubleArrow();
        $this->p($node->body);
        $this->pEnd('matchArm');
    }

    protected function pModifiers(Node $node) : void
    {
        if ($node->flags) {
            $this->list[] = new P\Modifiers($node->flags);
        }
    }

    protected function pName(Name $node) : void
    {
        $this->list[] = $this->name($node);
    }

    protected function pName_FullyQualified(Name\FullyQualified $node) : void
    {
        $this->list[] = '\\' . $this->name($node);
    }

    protected function pName_Relative(Name\Relative $node) : void
    {
        $this->list[] = 'namespace\\' . $this->name($node);
    }

    protected function pNewAnonymous(Stmt\Class_ $node, array $args)
    {
        $this->pAttributeGroups($node);
        $this->pModifiers($node);
        $this->list[] = new P\Class_(null, null);
        $this->list[] = new P\Args(count($args));
        $this->pSeparate('arg', $args);
        $this->list[] = new P\ArgsEnd(count($args));
        $this->pExtends($node);
        $this->pImplements($node);
        $this->pBody('closure');
        $this->p($node->stmts);
        $this->pBodyEnd('closure');
    }

    protected function pNewVariable(Node $node) : void
    {
        // TODO: This is not fully accurate.
        $this->pDereferenceLhs($node);
    }

    protected function pObjectProperty(Node $node) : void
    {
        if ($node instanceof Expr) {
            $this->pEmbrace($node);
        } else {
            $this->list[] = $this->name($node);
        }
    }

    protected function pParam(Node\Param $node) : void
    {
        $this->pAttributeGroups($node);
        $this->pModifiers($node);
        $this->pType($node);
        $this->pByRef($node);
        $this->pVariadic($node);
        $this->p($node->var);
        $this->pDefaultValue($node);
    }

    protected function pParams(Node $node)
    {
        $this->list[] = new P\Params(count($node->params));
        $this->pSeparate('param', $node->params);
        $this->list[] = new P\ParamsEnd(count($node->params));
    }

    protected function pPostfixOp(string $class, Node $node) : void
    {
        list($precedence, $associativity) = $this->precedenceMap[$class];
        $this->pPrec($node, $precedence, $associativity, -1);
        $this->list[] = new P\PostfixOp($class);
    }

    protected function pPrec(
        Node $node,
        int $parentPrecedence,
        int $parentAssociativity,
        int $childPosition,
    ) : void
    {
        $class = get_class($node);

        if (isset($this->precedenceMap[$class])) {
            $childPrecedence = $this->precedenceMap[$class][0];

            if (
                $childPrecedence > $parentPrecedence
                || $parentPrecedence === $childPrecedence
                && $parentAssociativity !== $childPosition
            ) {
                $this->list[] = new P\Precedence();
                $this->p($node);
                $this->list[] = $this->pEnd('precedence');
                return;
            }
        }

        $this->p($node);
    }

    protected function pPrefixOp(string $class, Node $node) : void
    {
        list($precedence, $associativity) = $this->precedenceMap[$class];
        $this->list[] = new P\PrefixOp($class);
        $this->pPrec($node, $precedence, $associativity, 1);
    }

    protected function pReturnType(Node $node)
    {
        if ($node->returnType) {
            $this->list[] = new P\ReturnType();
            $this->p($node->returnType);
        }
    }

    protected function pScalar_DNumber(Scalar\DNumber $node) : void
    {
        if (! is_finite($node->value)) {
            if ($node->value === INF) {
                $this->list[] = '\\INF';
                return;
            } elseif ($node->value === -INF) {
                $this->list[] = '-\\INF';
                return;
            } else {
                $this->list[] = '\\NAN';
                return;
            }
        }

        // Try to find a short full-precision representation
        $stringValue = sprintf('%.16G', $node->value);

        if ($node->value !== (float) $stringValue) {
            $stringValue = sprintf('%.17G', $node->value);
        }

        // %G is locale dependent and there exists no locale-independent alternative. We don't want
        // mess with switching locales here, so let's assume that a comma is the only non-standard
        // decimal separator we may encounter...
        $stringValue = str_replace(',', '.', $stringValue);

        // ensure that number is really printed as float
        $this->list[] = preg_match('/^-?[0-9]+$/', $stringValue) ? $stringValue
            . '.0'
         : $stringValue;
    }

    protected function pScalar_Encapsed(Scalar\Encapsed $node) : void
    {
        if ($node->getAttribute('kind') === Scalar\String_::KIND_HEREDOC) {
            $label = $node->getAttribute('docLabel');

            if (
                $label
                && ! $this->encapsedContainsEndLabel($node->parts, $label)
            ) {
                if (
                    count($node->parts) === 1
                    && $node->parts[0] instanceof Scalar\EncapsedStringPart
                    && $node->parts[0]->value === ''
                ) {
                    $this->list[] = new P\Heredoc($label);
                    $this->list[] = new P\HeredocEnd($label);
                    return;
                }

                $this->list[] = new P\Heredoc($label);
                $this->pEncapsList($node->parts, null);
                $this->list[] = new P\HeredocEnd($label);
                return;
            }
        }

        $this->list[] = '"';
        $this->pEncapsList($node->parts, '"');
        $this->list[] = '"';
    }

    protected function pScalar_EncapsedStringPart(
        Scalar\EncapsedStringPart $node,
    ) : void
    {
        throw new LogicException('Cannot directly print EncapsedStringPart');
    }

    protected function pScalar_LNumber(Scalar\LNumber $node) : void
    {
        $this->list[] = $this->scalar_LNumber($node);
    }

    protected function pScalar_MagicConst_Class(MagicConst\Class_ $node) : void
    {
        $this->list[] = '__CLASS__';
    }

    protected function pScalar_MagicConst_Dir(MagicConst\Dir $node) : void
    {
        $this->list[] = '__DIR__';
    }

    protected function pScalar_MagicConst_File(MagicConst\File $node) : void
    {
        $this->list[] = '__FILE__';
    }

    protected function pScalar_MagicConst_Function(
        MagicConst\Function_ $node,
    ) : void
    {
        $this->list[] = '__FUNCTION__';
    }

    protected function pScalar_MagicConst_Line(MagicConst\Line $node) : void
    {
        $this->list[] = '__LINE__';
    }

    protected function pScalar_MagicConst_Method(MagicConst\Method $node) : void
    {
        $this->list[] = '__METHOD__';
    }

    protected function pScalar_MagicConst_Namespace(
        MagicConst\Namespace_ $node,
    ) : void
    {
        $this->list[] = '__NAMESPACE__';
    }

    protected function pScalar_MagicConst_Trait(MagicConst\Trait_ $node) : void
    {
        $this->list[] = '__TRAIT__';
    }

    protected function pScalar_String(Scalar\String_ $node) : void
    {
        $kind = $node->getAttribute('kind', Scalar\String_::KIND_SINGLE_QUOTED);

        switch ($kind) {
            case Scalar\String_::KIND_NOWDOC:
                $label = $node->getAttribute('docLabel');

                if ($label && ! $this->containsEndLabel($node->value, $label)) {
                    $this->list[] = new P\Nowdoc($label);
                    $this->list[] = $node->value;
                    $this->list[] = new P\HeredocEnd($label);
                    return;
                }

            /* break missing intentionally */
            case Scalar\String_::KIND_SINGLE_QUOTED:
                $this->list[] = '\'' . addcslashes($node->value, '\'\\') . '\'';
                return;

            case Scalar\String_::KIND_HEREDOC:
                $label = $node->getAttribute('docLabel');

                if ($label && ! $this->containsEndLabel($node->value, $label)) {
                    $this->list[] = new P\Heredoc($label);

                    if ($value) {
                        $this->list[] = $this->escapeString($node->value, null);
                    }

                    $this->list = new P\HeredocEnd($label);
                }

            /* break missing intentionally */
            case Scalar\String_::KIND_DOUBLE_QUOTED:
                $this->list[] = '"'
                    . $this->escapeString($node->value, '"')
                    . '"'
                ;
                return;
        }

        throw new Exception('Invalid string kind');
    }

    protected function pSeparate(string $type, ?array $nodes) : void
    {
        if (! $nodes) {
            return;
        }

        foreach ($nodes as $node) {
            $this->p($node);
            $this->list[] = new P\Separator($type);
        }

        // remove last separator
        array_pop($this->list);
    }

    protected function pStmt_Break(Stmt\Break_ $node) : void
    {
        $num = $node->num ? $this->scalar_LNumber($node->num) : null;
        $this->list[] = new P\Break_($num);
    }

    protected function pStmt_Class(Stmt\Class_ $node) : void
    {
        $this->pAttributeGroups($node);
        $this->list[] = new P\Class_($node->flags, $this->name($node->name));
        $this->pExtends($node);
        $this->pImplements($node);
        $this->pBody('class');
        $this->p($node->stmts);
        $this->pBodyEnd('class');
    }

    protected function pStmt_ClassConst(Stmt\ClassConst $node) : void
    {
        $this->pAttributeGroups($node);
        $this->pModifiers($node);
        $this->list[] = new P\Const_();
        $this->pSeparate('const', $node->consts);
        $this->pEnd('const');
    }

    protected function pStmt_ClassMethod(Stmt\ClassMethod $node) : void
    {
        $this->pAttributeGroups($node);
        $this->list[] = new P\Function_($node->flags);
        $this->pByRef($node);
        $this->p($node->name);
        $this->pParams($node);
        $this->pReturnType($node);

        if ($node->stmts === null) {
            $this->pBodyEmpty('function');
        } else {
            $this->pBody('function');
            $this->p($node->stmts);
            $this->pBodyEnd('function');
        }
    }

    protected function pStmt_Const(Stmt\Const_ $node) : void
    {
        $this->list[] = new P\Const_();
        $this->pSeparate('const', $node->consts);
        $this->pEnd('const');
    }

    protected function pStmt_Continue(Stmt\Continue_ $node) : void
    {
        $num = $node->num ? $this->scalar_LNumber($node->num) : null;
        $this->list[] = new P\Continue_($num);
    }

    protected function pStmt_Declare(Stmt\Declare_ $node) : void
    {
        $this->list[] = new P\Declare_();
        $this->list[] = new P\Params(count($node->declares));
        $this->pSeparate('param', $node->declares);
        $this->list[] = new P\ParamsEnd(count($node->declares));

        if ($node->stmts !== null) {
            $this->pBody('declare');
            $this->p($node->stmts);
            $this->pBodyEnd('declare');
        } else {
            $this->pBodyEmpty('declare');
        }
    }

    protected function pStmt_DeclareDeclare(Stmt\DeclareDeclare $node) : void
    {
        $this->list[] = new P\DeclareDirective($this->name($node->key));
        $this->p($node->value);
    }

    protected function pStmt_Do(Stmt\Do_ $node) : void
    {
        $this->pBody('do');
        $this->p($node->stmts);
        $this->pBodyEnd('do');
        $this->pCond($node);
        $this->pEnd('do');
    }

    protected function pStmt_Echo(Stmt\Echo_ $node) : void
    {
        $this->list[] = new P\ReservedStmt('echo');
        $this->pSeparate('reservedStmt', $node->exprs);
        $this->pEnd('reservedStmt');
    }

    protected function pStmt_Enum(Stmt\Enum_ $node) : void
    {
        $this->pAttributeGroups($node);
        $this->list[] = new P\Enum_($this->name($node->name));

        if ($node->scalarType) {
            $this->list[] = new P\ReturnType();
            $this->list[] = $this->name($node->scalarType);
        }

        $this->pImplements($node);
        $this->pBody('enum');
        $this->p($node->stmts);
        $this->pBodyEnd('enum');
    }

    protected function pStmt_EnumCase(Stmt\EnumCase $node) : void
    {
        $this->pAttributeGroups($node);
        $this->list[] = new P\EnumCase($this->name($node->name));

        if ($node->expr) {
            $this->list[] = ' = ';
            $this->p($node->expr);
        }

        $this->pEnd('enumCase');
    }

    protected function pStmt_Expression(Stmt\Expression $node) : void
    {
        $this->p($node->expr);
        $this->pEnd('expr');
    }

    protected function pStmt_For(Stmt\For_ $node) : void
    {
        $this->list[] = new P\For_();
        $this->list[] = new P\Cond();
        $this->pSeparate('for', $node->init);
        $this->list[] = new P\Separator('forExpr', 1, 2);
        $this->pSeparate('for', $node->cond);
        $this->list[] = new P\Separator('forExpr', 2, 2);
        $this->pSeparate('for', $node->loop);
        $this->pEnd('cond');
        $this->pBody('for');
        $this->p($node->stmts);
        $this->pBodyEnd('for');
    }

    protected function pStmt_Foreach(Stmt\Foreach_ $node) : void
    {
        $this->list[] = new P\Foreach_();
        $this->list[] = new P\Cond();
        $this->p($node->expr);
        $this->list[] = ' as ';

        if ($node->keyVar) {
            $this->p($node->keyVar);
            $this->list[] = ' => ';
        }

        $this->pByRef($node);
        $this->p($node->valueVar);
        $this->pEnd('cond');
        $this->pBody('foreach');
        $this->p($node->stmts);
        $this->pBodyEnd('foreach');
    }

    protected function pStmt_Function(Stmt\Function_ $node) : void
    {
        $this->pAttributeGroups($node);
        $this->list[] = new P\Function_(null);
        $this->pByRef($node);
        $this->p($node->name);
        $this->pParams($node);
        $this->pReturnType($node);
        $this->pBody('function');
        $this->p($node->stmts);
        $this->pBodyEnd('function');
    }

    protected function pStmt_Global(Stmt\Global_ $node) : void
    {
        $this->list[] = new P\ReservedStmt('global');
        $this->pSeparate('reservedStmt', $node->vars);
        $this->pEnd('reservedStmt');
    }

    protected function pStmt_Goto(Stmt\Goto_ $node) : void
    {
        $this->list[] = new P\Goto_($this->name($node->name));
    }

    protected function pStmt_HaltCompiler(Stmt\HaltCompiler $node) : void
    {
        $this->list[] = new P\HaltCompiler();
        $this->list[] = $node->remaining;
    }

    protected function pStmt_If(Stmt\If_ $node) : void
    {
        $this->list[] = new P\If_();
        $this->pCond($node);
        $this->pBody('if');
        $this->p($node->stmts);

        foreach ($node->elseifs as $elseif) {
            $this->p($elseif);
        }

        if ($node->else) {
            $this->p($node->else);
        }

        $this->pEnd('if');
    }

    protected function pStmt_ElseIf(Stmt\ElseIf_ $node) : void
    {
        $this->list[] = new P\ElseIf_();
        $this->pCond($node);
        $this->pBody('elseif');
        $this->p($node->stmts);
    }

    protected function pStmt_Else(Stmt\Else_ $node) : void
    {
        $this->list[] = new P\Else_();
        $this->pBody('else');
        $this->p($node->stmts);
    }

    protected function pStmt_InlineHTML(Stmt\InlineHTML $node) : void
    {
        $this->list[] = new P\InlineHtml($node
            ->getAttribute('hasLeadingNewline', true)
        );
        $this->list[] = $node->value;
        $this->list[] = $this->pEnd('inlineHtml');
    }

    protected function pStmt_Interface(Stmt\Interface_ $node) : void
    {
        $this->pAttributeGroups($node);
        $this->list[] = new P\Interface_($this->name($node->name));
        $this->pExtends($node);
        $this->pBody('interface');
        $this->p($node->stmts);
        $this->pBodyEnd('interface');
    }

    protected function pStmt_Label(Stmt\Label $node) : void
    {
        $this->list[] = new P\Label($this->name($node->name));
    }

    protected function pStmt_Namespace(Stmt\Namespace_ $node) : void
    {
        $name = $node->name === null ? null : $this->name($node->name);
        $this->list[] = new P\Namespace_($name);

        if ($this->namespaceCount > 1) {
            $this->pBody('namespace');
            $this->p($node->stmts);
            $this->pBodyEnd('namespace');
        } else {
            $this->pBodyEmpty('namespace');
            $this->p($node->stmts);
        }
    }

    protected function pStmt_Property(Stmt\Property $node) : void
    {
        $this->pAttributeGroups($node);
        $this->list[] = new P\Property($node->flags);
        $this->pType($node);
        $this->pSeparate('property', $node->props);
        $this->pEnd('property');
    }

    protected function pStmt_PropertyProperty(
        Stmt\PropertyProperty $node,
    ) : void
    {
        // @todo consider infix/assign for this
        $this->p($node->name);
        $this->pDefaultValue($node);
    }

    protected function pStmt_Return(Stmt\Return_ $node) : void
    {
        $this->list[] = new P\Return_(! $node->expr);
        $this->p($node->expr);
        $this->pEnd('return');
    }

    protected function pStmt_Static(Stmt\Static_ $node) : void
    {
        $this->list[] = new P\ReservedStmt('static');
        $this->pSeparate('reservedStmt', $node->vars);
        $this->pEnd('reservedStmt');
    }

    protected function pStmt_StaticVar(Stmt\StaticVar $node) : void
    {
        $this->p($node->var);
        $this->pDefaultValue($node);
    }

    protected function pStmt_Switch(Stmt\Switch_ $node) : void
    {
        $this->list[] = new P\Switch_();
        $this->pCond($node);
        $this->list[] = new P\Body('switch');

        foreach ($node->cases as $case) {
            $this->p($case);
        }

        $this->list[] = new P\BodyEnd('switch');
    }

    protected function pStmt_Case(Stmt\Case_ $node) : void
    {
        if ($node->cond) {
            $this->list[] = new P\SwitchCase();
            $this->p($node->cond);
            $this->pEnd('switchCase');
        } else {
            $this->list[] = new P\SwitchCaseDefault();
        }

        if ($node->stmts) {
            $this->pBody('switchCase');
            $this->p($node->stmts);
            $this->pBodyEnd('switchCase');
        }
    }

    protected function pStmt_Throw(Stmt\Throw_ $node) : void
    {
        $this->list[] = new P\Throw_();
        $this->p($node->expr);
        $this->pEnd('throw');
    }

    protected function pStmt_Trait(Stmt\Trait_ $node) : void
    {
        $this->pAttributeGroups($node);
        $this->list[] = new P\Trait_($this->name($node->name));
        $this->pBody('trait');
        $this->p($node->stmts);
        $this->pBodyEnd('trait');
    }

    protected function pStmt_TraitUse(Stmt\TraitUse $node) : void
    {
        $this->list[] = new P\UseTrait();
        $this->pSeparate('useTrait', $node->traits);

        if (! $node->adaptations) {
            $this->pEnd('useTrait');
            return;
        }

        $this->list[] = new P\Body('useTrait');
        $this->p($node->adaptations);
        $this->list[] = new P\BodyEnd('useTrait');
    }

    protected function pStmt_TraitUseAdaptation_Alias(
        Stmt\TraitUseAdaptation\Alias $node,
    ) : void
    {
        $oldName = $node->trait ? $this->name($node->trait) : null;
        $newName = $node->newName ? $this->name($node->newName) : null;
        $this->list[] = new P\UseTraitAs($oldName, $this
            ->name($node->method)
        , $node->newModifier, $newName);
    }

    protected function pStmt_TraitUseAdaptation_Precedence(
        Stmt\TraitUseAdaptation\Precedence $node,
    ) : void
    {
        $this->list[] = new P\UseTraitInsteadof($this
            ->name($node->trait)
        , $this
            ->name($node->method)
        );
        $this->pSeparate('insteadof', $node->insteadof);
        $this->pEnd('useTraitInsteadOf');
    }

    protected function pStmt_TryCatch(Stmt\TryCatch $node) : void
    {
        $this->list[] = new P\Try_();
        $this->pBody('try');
        $this->p($node->stmts);

        foreach ($node->catches as $catch) {
            $this->p($catch);
        }

        if ($node->finally) {
            $this->p($node->finally);
        }

        $this->pEnd('try');
    }

    protected function pStmt_Catch(Stmt\Catch_ $node) : void
    {
        $this->list[] = new P\TryCatch();
        $this->list[] = new P\Args(count($node->types));
        $this->list[] = implode('|', $node->types);

        if ($node->var) {
            $this->list[] = ' ';
            $this->p($node->var);
        }

        $this->list[] = new P\ArgsEnd(count($node->types));
        $this->pBody('tryCatch');
        $this->p($node->stmts);
    }

    protected function pStmt_Finally(Stmt\Finally_ $node) : void
    {
        $this->list[] = new P\TryFinally();
        $this->pBody('tryFinally');
        $this->p($node->stmts);
    }

    protected function pStmt_Use(Stmt\Use_ $node) : void
    {
        $this->list[] = new P\UseImport($this->useType($node), null);
        $this->pSeparate('use', $node->uses);
        $this->list[] = new P\UseImportEnd(null);
    }

    protected function pStmt_GroupUse(Stmt\GroupUse $node) : void
    {
        $prefix = $this->name($node->prefix);
        $this->list[] = new P\UseImport($this->useType($node), $prefix);
        $this->pSeparate('use', $node->uses);
        $this->list[] = new P\UseImportEnd($prefix);
    }

    protected function pStmt_UseUse(Stmt\UseUse $node) : void
    {
        $type = $this->useType($node);
        $this->list[] = $type . $this->name($node->name);

        if ($node->alias) {
            $this->list[] = ' as ' . $node->alias;
        }
    }

    protected function pStmt_Unset(Stmt\Unset_ $node) : void
    {
        $this->list[] = new P\Unset_();
        $this->pSeparate('arg', $node->vars);
        $this->pEnd('unset');
    }

    protected function pStmt_While(Stmt\While_ $node) : void
    {
        $this->list[] = new P\While_();
        $this->pCond($node);
        $this->pBody('while');
        $this->p($node->stmts);
        $this->pBodyEnd('while');
    }

    protected function pType(Node $node) : void
    {
        $type = $this->type($node);

        if ($type) {
            $this->list[] = $type . ' ';
        }
    }

    protected function pIntersectionType(Node\IntersectionType $node) : void
    {
        $this->list[] = $this->intersectionType($node);
    }

    protected function pNullableType(Node\NullableType $node) : void
    {
        $this->list[] = $this->nullableType($node);
    }

    protected function pUnionType(Node\UnionType $node) : void
    {
        $this->list[] = $this->unionType($node);
    }

    protected function pUnpack(Node $node) : void
    {
        if ($node->unpack) {
            $this->list[] = '...';
        }
    }

    protected function pVariadic(Node $node) : void
    {
        if ($node->variadic) {
            $this->list[] = '...';
        }
    }

    protected function pVarLikeIdentifier(Node\VarLikeIdentifier $node) : void
    {
        $this->list[] = '$' . $node->name;
    }

    // Helpers
    protected function callLhsRequiresParens(Node $node) : bool
    {
        return ! (
            $node instanceof Node\Name
                || $node instanceof Expr\Variable
                || $node instanceof Expr\ArrayDimFetch
                || $node instanceof Expr\FuncCall
                || $node instanceof Expr\MethodCall
                || $node instanceof Expr\NullsafeMethodCall
                || $node instanceof Expr\StaticCall
                || $node instanceof Expr\Array_
        );
    }

    protected function containsEndLabel(
        $string,
        $label,
        $atStart = true,
        $atEnd = true,
    ) : bool
    {
        $start = $atStart ? '(?:^|[\\r\\n])' : '[\\r\\n]';
        $end = $atEnd ? '(?:$|[;\\r\\n])' : '[;\\r\\n]';
        return false !== strpos($string, $label)
            && preg_match('/' . $start . $label . $end . '/', $string)
        ;
    }

    protected function dereferenceLhsRequiresParens(Node $node) : bool
    {
        return ! (
            $node instanceof Expr\Variable
                || $node instanceof Node\Name
                || $node instanceof Expr\ArrayDimFetch
                || $node instanceof Expr\PropertyFetch
                || $node instanceof Expr\NullsafePropertyFetch
                || $node instanceof Expr\StaticPropertyFetch
                || $node instanceof Expr\FuncCall
                || $node instanceof Expr\MethodCall
                || $node instanceof Expr\NullsafeMethodCall
                || $node instanceof Expr\StaticCall
                || $node instanceof Expr\Array_
                || $node instanceof Scalar\String_
                || $node instanceof Expr\ConstFetch
                || $node instanceof Expr\ClassConstFetch
        );
    }

    protected function encapsedContainsEndLabel(array $parts, $label) : bool
    {
        foreach ($parts as $i => $part) {
            $atStart = $i === 0;
            $atEnd = $i === count($parts) - 1;

            if (
                $part instanceof Scalar\EncapsedStringPart
                && $this
                    ->containsEndLabel($part->value, $label, $atStart, $atEnd)

            ) {
                return true;
            }
        }

        return false;
    }

    protected function escapeString($string, $quote) : string
    {
        if (null === $quote) {
            // For doc strings, don't escape newlines
            $escaped = addcslashes($string, "\t\f\v\$\\");
        } else {
            $escaped = addcslashes($string, "\n\r\t\f\v\$" . $quote . "\\");
        }

        // Escape control characters and non-UTF-8 characters.
        // Regex based on https://stackoverflow.com/a/11709412/385378.
        $regex = '/(
              [\\x00-\\x08\\x0E-\\x1F] # Control characters
            | [\\xC0-\\xC1] # Invalid UTF-8 Bytes
            | [\\xF5-\\xFF] # Invalid UTF-8 Bytes
            | \\xE0(?=[\\x80-\\x9F]) # Overlong encoding of prior code point
            | \\xF0(?=[\\x80-\\x8F]) # Overlong encoding of prior code point
            | [\\xC2-\\xDF](?![\\x80-\\xBF]) # Invalid UTF-8 Sequence Start
            | [\\xE0-\\xEF](?![\\x80-\\xBF]{2}) # Invalid UTF-8 Sequence Start
            | [\\xF0-\\xF4](?![\\x80-\\xBF]{3}) # Invalid UTF-8 Sequence Start
            | (?<=[\\x00-\\x7F\\xF5-\\xFF])[\\x80-\\xBF] # Invalid UTF-8 Sequence Middle
            | (?<![\\xC2-\\xDF]|[\\xE0-\\xEF]|[\\xE0-\\xEF][\\x80-\\xBF]|[\\xF0-\\xF4]|[\\xF0-\\xF4][\\x80-\\xBF]|[\\xF0-\\xF4][\\x80-\\xBF]{2})[\\x80-\\xBF] # Overlong Sequence
            | (?<=[\\xE0-\\xEF])[\\x80-\\xBF](?![\\x80-\\xBF]) # Short 3 byte sequence
            | (?<=[\\xF0-\\xF4])[\\x80-\\xBF](?![\\x80-\\xBF]{2}) # Short 4 byte sequence
            | (?<=[\\xF0-\\xF4][\\x80-\\xBF])[\\x80-\\xBF](?![\\x80-\\xBF]) # Short 4 byte sequence (2)
        )/x';
        $callback = function ($matches) {
            assert(strlen($matches[0]) === 1);
            $hex = dechex(ord($matches[0]));
            return '\\x' . str_pad($hex, 2, '0', STR_PAD_LEFT);
        };
        return preg_replace_callback($regex, $callback, $escaped);
    }

    protected function name(
        Node\Identifier|Node\Name|Name\FullyQualified|Name\Relative $node,
    ) : string
    {
        if ($node instanceof Node\Identifier) {
            return $node->name;
        } else {
            return implode('\\', $node->parts);
        }
    }

    protected function type(Node $node) : string
    {
        if (! $node->type) {
            return '';
        }

        if ($node->type instanceof Node\NullableType) {
            return $this->nullableType($node->type);
        }

        if ($node->type instanceof Node\IntersectionType) {
            return $this->intersectionType($node->type);
        }

        if ($node->type instanceof Node\UnionType) {
            return $this->unionType($node->type);
        }

        return $this->name($node->type);
    }

    protected function intersectionType(Node\IntersectionType $node) : string
    {
        $types = [];

        foreach ($node->types as $typeNode) {
            $types[] = $this->name($typeNode);
        }

        return implode('&', $types);
    }

    protected function nullableType(Node\NullableType $node) : string
    {
        return '?' . $this->name($node->type);
    }

    protected function unionType(Node\UnionType $node) : string
    {
        $types = [];

        foreach ($node->types as $typeNode) {
            if ($typeNode instanceof Node\IntersectionType) {
                $types[] = '(' . $this->intersectionType($typeNode) . ')';
            } else {
                $types[] = $this->name($typeNode);
            }
        }

        return implode('|', $types);
    }

    protected function scalar_LNumber(Scalar\LNumber $node) : string
    {
        if ($node->value === -PHP_INT_MAX - 1) {
            // PHP_INT_MIN cannot be represented as a literal,
            // because the sign is not part of the literal
            return '(-' . PHP_INT_MAX . '-1)';
        }

        $kind = $node->getAttribute('kind', Scalar\LNumber::KIND_DEC);

        if (Scalar\LNumber::KIND_DEC === $kind) {
            return (string) $node->value;
        }

        if ($node->value < 0) {
            $sign = '-';
            $str = (string) -$node->value;
        } else {
            $sign = '';
            $str = (string) $node->value;
        }

        switch ($kind) {
            case Scalar\LNumber::KIND_BIN:
                return $sign . '0b' . base_convert($str, 10, 2);

            case Scalar\LNumber::KIND_OCT:
                return $sign . '0' . base_convert($str, 10, 8);

            case Scalar\LNumber::KIND_HEX:
                return $sign . '0x' . base_convert($str, 10, 16);
        }

        throw new Exception('Invalid number kind');
    }

    protected function useType(Node $node) : string
    {
        switch ($node->type) {
            case Stmt\Use_::TYPE_CONSTANT:
                return 'const';

            case Stmt\Use_::TYPE_FUNCTION:
                return 'function';

            default:
                return '';
        }
    }
}
