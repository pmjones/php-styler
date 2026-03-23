<?php
declare(strict_types=1);

namespace PhpStyler\Format;

use PhpStyler\Rule;
use PhpStyler\Rule\LineRule;
use PhpStyler\Rule\TokenRule;
use PhpStyler\Token;

/**
 * @phpstan-import-type styles_array from Format
 * @phpstan-import-type parse_as_array from Format
 */
class PlainFormat implements Format
{
    /**
     * @var styles_array
     * @php-styler-expansive
     */
    public protected(set) array $styles = [
        Token\TAbstractMethodEndSemicolon::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
            'blankLineAfter' => true,
        ],
        Token\TAmpersandNotFollowedByVarOrVararg::class => [
            'spaceAfter' => true,
        ],
        Token\TAnonymousClass::class => [
            'spaceAfter' => true,
        ],
        Token\TAnonymousClassArgsClosingParen::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
        ],
        Token\TAnonymousClosingBrace::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
        ],
        Token\TAnonymousFunction::class => [
            'spaceAfter' => true,
        ],
        Token\TAnonymousOpeningBrace::class => [
            'lineBreakAfter' => true,
        ],
        Token\TArgsClosingParen::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
        ],
        Token\TArgsComma::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
        ],
        Token\TArgsOpeningParen::class => [
            'spaceBefore' => false,
        ],
        Token\TArray::class => [
            'spaceAfter' => true,
            'case' => 'strtolower',
        ],
        Token\TArrayClosingBracket::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
        ],
        Token\TArrayComma::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
        ],
        Token\TArrayConstruct::class => [
            'spaceAfter' => false,
            'case' => 'strtolower',
        ],
        Token\TArrayConstructClosingParen::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
        ],
        Token\TArrayConstructOpeningParen::class => [
            'spaceBefore' => false,
        ],
        Token\TArrayElementClosingBracket::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
        ],
        Token\TArrayElementOpeningBracket::class => [
            'spaceBefore' => false,
        ],
        Token\TAs::class => [
            'spaceBefore' => true,
            'spaceAfter' => true,
        ],
        Token\TAssign::class => [
            'spaceBefore' => true,
            'spaceAfter' => true,
        ],
        Token\TAssignConst::class => [
            'spaceBefore' => true,
            'spaceAfter' => true,
        ],
        Token\TAssignDefault::class => [
            'spaceBefore' => true,
            'spaceAfter' => true,
        ],
        Token\TAssignDirective::class => [
            'spaceBefore' => false,
            'spaceAfter' => false,
        ],
        Token\TAssignProperty::class => [
            'spaceBefore' => true,
            'spaceAfter' => true,
        ],
        Token\TAttributeClosingBracket::class => [
            'spaceBefore' => false,
            'lineBreakAfter' => true,
        ],
        Token\TBacktickClosing::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
        ],
        Token\TBinaryMinus::class => [
            'spaceBefore' => true,
            'spaceAfter' => true,
        ],
        Token\TBinaryPlus::class => [
            'spaceBefore' => true,
            'spaceAfter' => true,
        ],
        Token\TBitwiseAnd::class => [
            'spaceBefore' => true,
            'spaceAfter' => true,
        ],
        Token\TBitwiseOr::class => [
            'spaceAfter' => true,
        ],
        Token\TCase::class => [
            'spaceAfter' => true,
        ],
        Token\TCaseAfterCase::class => [
            'spaceAfter' => true,
        ],
        Token\TCaseColon::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
            'lineBreakAfter' => true,
        ],
        Token\TCaseFallthroughColon::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
            'lineBreakAfter' => true,
        ],
        Token\TCatch::class => [
            'spaceAfter' => true,
        ],
        Token\TCatchClosingBrace::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
            'blankLineAfter' => true,
        ],
        Token\TCatchContinuationBrace::class => [
            'spaceBefore' => true,
            'spaceAfter' => true,
        ],
        Token\TCatchOpeningBrace::class => [
            'lineBreakAfter' => true,
        ],
        Token\TClass::class => [
            'spaceAfter' => true,
        ],
        Token\TClassClosingBrace::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
            'blankLineAfter' => true,
        ],
        Token\TClassName::class => [
            'spaceAfter' => true,
        ],
        Token\TClassOpeningBrace::class => [
            'lineBreakBefore' => true,
            'lineBreakAfter' => true,
        ],
        Token\TClasslikeClosingBrace::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
            'blankLineAfter' => true,
        ],
        Token\TClasslikeOpeningBrace::class => [
            'lineBreakBefore' => true,
            'lineBreakAfter' => true,
        ],
        Token\TClosingBrace::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
        ],
        Token\TClosingParen::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
        ],
        Token\TComma::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
        ],
        Token\TCommentHashed::class => [
            'lineBreakBefore' => true,
            'lineBreakAfter' => true,
        ],
        Token\TCommentHashedBlankLine::class => [
            'spaceBefore' => true,
            'blankLineAfter' => true,
        ],
        Token\TCommentHashedLineBreak::class => [
            'spaceBefore' => true,
            'lineBreakAfter' => true,
        ],
        Token\TCommentHashedMidStatement::class => [
            'spaceAfter' => true,
        ],
        Token\TCommentSlashed::class => [
            'lineBreakBefore' => true,
            'lineBreakAfter' => true,
        ],
        Token\TCommentSlashedBlankLine::class => [
            'spaceBefore' => true,
            'blankLineAfter' => true,
        ],
        Token\TCommentSlashedLineBreak::class => [
            'spaceBefore' => true,
            'lineBreakAfter' => true,
        ],
        Token\TCommentSlashedMidStatement::class => [
            'spaceAfter' => true,
        ],
        Token\TCommentStarred::class => [
            'lineBreakBefore' => true,
            'lineBreakAfter' => true,
        ],
        Token\TCommentStarredBlankLine::class => [
            'spaceBefore' => true,
            'blankLineAfter' => true,
        ],
        Token\TCommentStarredLineBreak::class => [
            'spaceBefore' => true,
            'lineBreakAfter' => true,
        ],
        Token\TCommentStarredMidStatement::class => [
            'spaceAfter' => true,
        ],
        Token\TConst::class => [
            'spaceAfter' => true,
        ],
        Token\TConstComma::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
        ],
        Token\TConstEndSemicolon::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
            'blankLineAfter' => true,
        ],
        Token\TConstName::class => [
            'spaceAfter' => true,
        ],
        Token\TConstantName::class => [
            'spaceAfter' => true,
        ],
        Token\TContinuationBraceless::class => [
            'spaceAfter' => true,
        ],
        Token\TCurlyClose::class => [
            'spaceBefore' => false,
            'spaceAfter' => false,
        ],
        Token\TCurlyOpen::class => [
            'spaceBefore' => false,
            'spaceAfter' => false,
        ],
        Token\TDeclare::class => [
            'spaceAfter' => true,
        ],
        Token\TDeclareClosingBrace::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
            'lineBreakAfter' => true,
        ],
        Token\TDeclareColon::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
            'lineBreakAfter' => true,
        ],
        Token\TDeclareDirective::class => [
            'spaceAfter' => false,
        ],
        Token\TDeclareDirectivesClosingParen::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
        ],
        Token\TDeclareDirectivesComma::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
        ],
        Token\TDeclareDirectivesOpeningParen::class => [
            'spaceBefore' => false,
        ],
        Token\TDeclareEndSemicolon::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
            'blankLineAfter' => true,
        ],
        Token\TDeclareOpeningBrace::class => [
            'lineBreakAfter' => true,
        ],
        Token\TDefault::class => [
            'spaceAfter' => true,
        ],
        Token\TDefaultAfterCase::class => [
            'spaceAfter' => true,
        ],
        Token\TDefaultCase::class => [
            'spaceAfter' => true,
        ],
        Token\TDo::class => [
            'spaceAfter' => true,
        ],
        Token\TDoContinuationBrace::class => [
            'spaceBefore' => true,
            'spaceAfter' => true,
        ],
        Token\TDoOpeningBrace::class => [
            'lineBreakAfter' => true,
        ],
        Token\TDocComment::class => [
            'lineBreakAfter' => true,
            'blankLineBefore' => true,
        ],
        Token\TDocCommentBlankLine::class => [
            'spaceBefore' => true,
            'blankLineAfter' => true,
        ],
        Token\TDocCommentLineBreak::class => [
            'spaceBefore' => true,
            'lineBreakAfter' => true,
        ],
        Token\TDocCommentMidStatement::class => [
            'spaceAfter' => true,
        ],
        Token\TDollarCloseCurlyBraces::class => [
            'spaceBefore' => false,
            'spaceAfter' => false,
        ],
        Token\TDollarOpenCurlyBraces::class => [
            'spaceBefore' => false,
            'spaceAfter' => false,
        ],
        Token\TDoubleArrow::class => [
            'spaceBefore' => true,
            'spaceAfter' => true,
        ],
        Token\TDoubleQuoteClosing::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
        ],
        Token\TDynamicMemberClosingBrace::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
        ],
        Token\TDynamicVariableClosingBrace::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
        ],
        Token\TEcho::class => [
            'spaceBefore' => true,
            'spaceAfter' => true,
        ],
        Token\TEchoComma::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
        ],
        Token\TEchoEndSemicolon::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
            'lineBreakAfter' => true,
        ],
        Token\TEllipsis::class => [
            'spaceAfter' => true,
        ],
        Token\TElse::class => [
            'spaceAfter' => true,
        ],
        Token\TElseClosingBrace::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
            'blankLineAfter' => true,
        ],
        Token\TElseClosingBraceless::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
            'lineBreakAfter' => true,
        ],
        Token\TElseColon::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
            'lineBreakAfter' => true,
        ],
        Token\TElseOpeningBrace::class => [
            'lineBreakAfter' => true,
        ],
        Token\TElseif::class => [
            'spaceAfter' => true,
        ],
        Token\TElseifClosingBrace::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
            'blankLineAfter' => true,
        ],
        Token\TElseifClosingBraceless::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
            'lineBreakAfter' => true,
        ],
        Token\TElseifClosingParen::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
        ],
        Token\TElseifColon::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
            'lineBreakAfter' => true,
        ],
        Token\TElseifContinuationBrace::class => [
            'spaceBefore' => true,
            'spaceAfter' => true,
        ],
        Token\TElseifContinuationBraceless::class => [
            'spaceAfter' => true,
        ],
        Token\TElseifOpeningBrace::class => [
            'lineBreakAfter' => true,
        ],
        Token\TElvisColon::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
        ],
        Token\TElvisEndSemicolon::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
            'lineBreakAfter' => true,
        ],
        Token\TEncapsedArrayElementClosingBracket::class => [
            'spaceBefore' => false,
            'spaceAfter' => false,
        ],
        Token\TEncapsedArrayElementOpeningBracket::class => [
            'spaceBefore' => false,
            'spaceAfter' => false,
        ],
        Token\TEncapsedNullsafeObjectOperator::class => [
            'spaceBefore' => false,
            'spaceAfter' => false,
        ],
        Token\TEncapsedObjectOperator::class => [
            'spaceBefore' => false,
            'spaceAfter' => false,
        ],
        Token\TEncapsedPropertyAccessName::class => [
            'spaceBefore' => false,
            'spaceAfter' => false,
        ],
        Token\TEncapsedVariable::class => [
            'spaceBefore' => false,
            'spaceAfter' => false,
        ],
        Token\TEnddeclare::class => [
            'spaceAfter' => true,
        ],
        Token\TEndfor::class => [
            'spaceAfter' => true,
        ],
        Token\TEndforeach::class => [
            'spaceAfter' => true,
        ],
        Token\TEndif::class => [
            'spaceAfter' => true,
        ],
        Token\TEndswitch::class => [
            'spaceAfter' => true,
        ],
        Token\TEndwhile::class => [
            'spaceAfter' => true,
        ],
        Token\TEnum::class => [
            'spaceAfter' => true,
        ],
        Token\TEnumCase::class => [
            'spaceAfter' => true,
        ],
        Token\TEnumCaseEndSemicolon::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
            'blankLineAfter' => true,
        ],
        Token\TEnumCaseName::class => [
            'spaceAfter' => true,
        ],
        Token\TEnumClosingBrace::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
            'blankLineAfter' => true,
        ],
        Token\TEnumName::class => [
            'spaceAfter' => true,
        ],
        Token\TEnumOpeningBrace::class => [
            'lineBreakBefore' => true,
            'lineBreakAfter' => true,
        ],
        Token\TExpressionClosingParen::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
        ],
        Token\TExtendsComma::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
        ],
        Token\TFalse::class => [
            'case' => 'strtolower',
        ],
        Token\TFinally::class => [
            'spaceAfter' => true,
        ],
        Token\TFinallyClosingBrace::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
            'blankLineAfter' => true,
        ],
        Token\TFinallyOpeningBrace::class => [
            'lineBreakAfter' => true,
        ],
        Token\TFirstClassCallableEllipsis::class => [
            'spaceAfter' => true,
        ],
        Token\TFn::class => [
            'spaceAfter' => true,
        ],
        Token\TFnDoubleArrow::class => [
            'spaceBefore' => true,
            'spaceAfter' => true,
        ],
        Token\TFnEndSemicolon::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
            'lineBreakAfter' => true,
        ],
        Token\TFor::class => [
            'spaceAfter' => true,
        ],
        Token\TForClosingBrace::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
            'blankLineAfter' => true,
        ],
        Token\TForClosingBraceless::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
            'lineBreakAfter' => true,
        ],
        Token\TForClosingParen::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
        ],
        Token\TForColon::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
            'lineBreakAfter' => true,
        ],
        Token\TForComma::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
        ],
        Token\TForOpeningBrace::class => [
            'lineBreakAfter' => true,
        ],
        Token\TForSemicolon::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
        ],
        Token\TForeach::class => [
            'spaceAfter' => true,
        ],
        Token\TForeachAs::class => [
            'spaceBefore' => true,
            'spaceAfter' => true,
        ],
        Token\TForeachClosingBrace::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
            'blankLineAfter' => true,
        ],
        Token\TForeachClosingBraceless::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
            'lineBreakAfter' => true,
        ],
        Token\TForeachClosingParen::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
        ],
        Token\TForeachColon::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
            'lineBreakAfter' => true,
        ],
        Token\TForeachOpeningBrace::class => [
            'lineBreakAfter' => true,
        ],
        Token\TFullyQualifiedName::class => [
            'spaceAfter' => true,
        ],
        Token\TFunction::class => [
            'spaceAfter' => true,
        ],
        Token\TFunctionCallFullyQualified::class => [
            'spaceAfter' => true,
        ],
        Token\TFunctionCallName::class => [
            'spaceAfter' => true,
        ],
        Token\TFunctionCallQualified::class => [
            'spaceAfter' => true,
        ],
        Token\TFunctionCallRelative::class => [
            'spaceAfter' => true,
        ],
        Token\TFunctionClosingBrace::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
            'blankLineAfter' => true,
        ],
        Token\TFunctionOpeningBrace::class => [
            'lineBreakBefore' => true,
            'lineBreakAfter' => true,
        ],
        Token\TGlobal::class => [
            'spaceAfter' => true,
        ],
        Token\TGlobalComma::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
        ],
        Token\TGlobalEndSemicolon::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
            'lineBreakAfter' => true,
        ],
        Token\TGotoLabel::class => [
            'spaceAfter' => true,
        ],
        Token\TGotoLabelColon::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
            'lineBreakAfter' => true,
        ],
        Token\THaltCompiler::class => [
            'spaceAfter' => true,
        ],
        Token\THaltCompilerSemicolon::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
        ],
        Token\THeredocEnd::class => [
            'spaceAfter' => true,
        ],
        Token\THeredocStart::class => [
            'spaceBefore' => true,
            'spaceAfter' => true,
        ],
        Token\TIf::class => [
            'spaceBefore' => true,
            'spaceAfter' => true,
        ],
        Token\TIfClosingBrace::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
            'blankLineAfter' => true,
        ],
        Token\TIfClosingBraceless::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
            'lineBreakAfter' => true,
        ],
        Token\TIfClosingParen::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
        ],
        Token\TIfColon::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
            'lineBreakAfter' => true,
        ],
        Token\TIfContinuationBrace::class => [
            'spaceBefore' => true,
            'spaceAfter' => true,
        ],
        Token\TIfContinuationBraceless::class => [
            'spaceAfter' => true,
        ],
        Token\TIfOpeningBrace::class => [
            'lineBreakAfter' => true,
        ],
        Token\TImplements::class => [
            'spaceAfter' => true,
        ],
        Token\TImplementsComma::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
        ],
        Token\TInlineAttributeClosingBracket::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
        ],
        Token\TInlineHtml::class => [
            'spaceBefore' => false,
            'spaceAfter' => false,
        ],
        Token\TInsteadofComma::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
        ],
        Token\TInterface::class => [
            'spaceAfter' => true,
        ],
        Token\TInterfaceClosingBrace::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
            'blankLineAfter' => true,
        ],
        Token\TInterfaceName::class => [
            'spaceAfter' => true,
        ],
        Token\TInterfaceOpeningBrace::class => [
            'lineBreakBefore' => true,
            'lineBreakAfter' => true,
        ],
        Token\TIntersection::class => [
            'spaceBefore' => false,
            'spaceAfter' => false,
        ],
        Token\TLoopEmptyClosingParen::class => [
            'spaceAfter' => true,
        ],
        Token\TLoopEmptySemicolon::class => [
            'spaceAfter' => true,
        ],
        Token\TMatch::class => [
            'spaceBefore' => true,
            'spaceAfter' => true,
        ],
        Token\TMatchArmComma::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
        ],
        Token\TMatchClosingBrace::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
        ],
        Token\TMatchClosingParen::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
        ],
        Token\TMatchDoubleArrow::class => [
            'spaceBefore' => true,
            'spaceAfter' => true,
        ],
        Token\TMatchOpeningBrace::class => [
            'lineBreakAfter' => true,
        ],
        Token\TMatchReturnComma::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
            'lineBreakAfter' => true,
        ],
        Token\TMemberDoubleColon::class => [
            'spaceBefore' => false,
            'spaceAfter' => false,
        ],
        Token\TMethodCallName::class => [
            'spaceAfter' => true,
        ],
        Token\TNamedArgColon::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
        ],
        Token\TNamedArgName::class => [
            'spaceAfter' => true,
        ],
        Token\TNamespace::class => [
            'spaceAfter' => true,
        ],
        Token\TNamespaceClosingBrace::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
            'lineBreakAfter' => true,
        ],
        Token\TNamespaceConstEndSemicolon::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
            'lineBreakAfter' => true,
        ],
        Token\TNamespaceEndSemicolon::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
            'blankLineAfter' => true,
        ],
        Token\TNamespaceOpeningBrace::class => [
            'lineBreakBefore' => true,
            'lineBreakAfter' => true,
        ],
        Token\TNamespaceSeparator::class => [
            'spaceBefore' => false,
            'spaceAfter' => false,
        ],
        Token\TNull::class => [
            'case' => 'strtolower',
        ],
        Token\TNullsafeObjectOperator::class => [
            'spaceBefore' => false,
            'spaceAfter' => false,
        ],
        Token\TObjectOperator::class => [
            'spaceBefore' => false,
            'spaceAfter' => false,
        ],
        Token\TOpeningBraceless::class => [
            'lineBreakAfter' => true,
        ],
        Token\TParamsClosingParen::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
        ],
        Token\TParamsComma::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
        ],
        Token\TPhpOpeningTag::class => [
            'spaceAfter' => true,
            'lineBreakAfter' => true,
        ],
        Token\TPhpOpeningTagInline::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
        ],
        Token\TPipe::class => [
            'spaceAfter' => true,
        ],
        Token\TPostDecrement::class => [
            'spaceBefore' => true,
            'spaceAfter' => true,
        ],
        Token\TPostIncrement::class => [
            'spaceBefore' => true,
            'spaceAfter' => true,
        ],
        Token\TPreDecrement::class => [
            'spaceBefore' => true,
            'spaceAfter' => true,
        ],
        Token\TPreIncrement::class => [
            'spaceBefore' => true,
            'spaceAfter' => true,
        ],
        Token\TPropertyAccessName::class => [
            'spaceAfter' => true,
        ],
        Token\TPropertyComma::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
        ],
        Token\TPropertyEndSemicolon::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
            'blankLineAfter' => true,
        ],
        Token\TPropertyHookGet::class => [
            'spaceAfter' => true,
        ],
        Token\TPropertyHookGetClosingBrace::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
            'lineBreakAfter' => true,
        ],
        Token\TPropertyHookGetDoubleArrow::class => [
            'spaceBefore' => true,
            'spaceAfter' => true,
        ],
        Token\TPropertyHookGetOpeningBrace::class => [
            'lineBreakAfter' => true,
        ],
        Token\TPropertyHookGetSemicolon::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
            'lineBreakAfter' => true,
        ],
        Token\TPropertyHookSet::class => [
            'spaceAfter' => true,
        ],
        Token\TPropertyHookSetClosingBrace::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
            'lineBreakAfter' => true,
        ],
        Token\TPropertyHookSetClosingParen::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
        ],
        Token\TPropertyHookSetDoubleArrow::class => [
            'spaceBefore' => true,
            'spaceAfter' => true,
        ],
        Token\TPropertyHookSetOpeningBrace::class => [
            'lineBreakAfter' => true,
        ],
        Token\TPropertyHookSetOpeningParen::class => [
            'spaceBefore' => false,
        ],
        Token\TPropertyHookSetSemicolon::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
            'lineBreakAfter' => true,
        ],
        Token\TPropertyHooksClosingBrace::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
            'blankLineAfter' => true,
        ],
        Token\TPropertyHooksOpeningBrace::class => [
            'lineBreakAfter' => true,
        ],
        Token\TQualifiedName::class => [
            'spaceAfter' => true,
        ],
        Token\TReference::class => [
            'spaceAfter' => false,
        ],
        Token\TRelativeName::class => [
            'spaceAfter' => true,
        ],
        Token\TReturnColon::class => [
            'spaceBefore' => true,
            'spaceAfter' => true,
        ],
        Token\TSemicolon::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
            'lineBreakAfter' => true,
        ],
        Token\TDoWhileEndSemicolon::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
            'lineBreakAfter' => true,
        ],
        Token\TSpreadEllipsis::class => [
            'spaceAfter' => false,
        ],
        Token\TStatic::class => [
            'spaceBefore' => true,
            'spaceAfter' => true,
        ],
        Token\TStaticBinding::class => [
            'spaceAfter' => true,
        ],
        Token\TStaticComma::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
        ],
        Token\TStaticMemberName::class => [
            'spaceAfter' => true,
        ],
        Token\TStaticMethodCallName::class => [
            'spaceAfter' => true,
        ],
        Token\TStaticType::class => [
            'spaceBefore' => true,
            'spaceAfter' => true,
        ],
        Token\TStaticVar::class => [
            'spaceBefore' => true,
            'spaceAfter' => true,
        ],
        Token\TStaticVarEndSemicolon::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
            'lineBreakAfter' => true,
        ],
        Token\TString::class => [
            'spaceAfter' => true,
        ],
        Token\TStringFragment::class => [
            'spaceBefore' => false,
            'spaceAfter' => false,
        ],
        Token\TStringNumericIndex::class => [
            'spaceAfter' => false,
        ],
        Token\TSwitch::class => [
            'spaceAfter' => true,
        ],
        Token\TSwitchAfterCaseClosingBrace::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
            'blankLineAfter' => true,
        ],
        Token\TSwitchClosingBrace::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
            'blankLineAfter' => true,
        ],
        Token\TSwitchClosingParen::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
        ],
        Token\TSwitchColon::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
            'lineBreakAfter' => true,
        ],
        Token\TSwitchOpeningBrace::class => [
            'lineBreakAfter' => true,
        ],
        Token\TTernaryColon::class => [
            'spaceBefore' => true,
            'spaceAfter' => true,
        ],
        Token\TTernaryEndSemicolon::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
            'lineBreakAfter' => true,
        ],
        Token\TTernaryQuestion::class => [
            'spaceBefore' => true,
            'spaceAfter' => true,
        ],
        Token\TTrait::class => [
            'spaceAfter' => true,
        ],
        Token\TTraitAlias::class => [
            'spaceAfter' => true,
        ],
        Token\TTraitClosingBrace::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
            'blankLineAfter' => true,
        ],
        Token\TTraitName::class => [
            'spaceAfter' => true,
        ],
        Token\TTraitOpeningBrace::class => [
            'lineBreakBefore' => true,
            'lineBreakAfter' => true,
        ],
        Token\TTrue::class => [
            'case' => 'strtolower',
        ],
        Token\TTry::class => [
            'spaceAfter' => true,
        ],
        Token\TTryContinuationBrace::class => [
            'spaceBefore' => true,
            'spaceAfter' => true,
        ],
        Token\TTryOpeningBrace::class => [
            'lineBreakAfter' => true,
        ],
        Token\TUnion::class => [
            'spaceBefore' => false,
            'spaceAfter' => false,
        ],
        Token\TUnknownString::class => [
            'spaceAfter' => true,
        ],
        Token\TUnqualifiedName::class => [
            'spaceAfter' => true,
        ],
        Token\TUse::class => [
            'spaceAfter' => true,
        ],
        Token\TUseAlias::class => [
            'spaceAfter' => true,
        ],
        Token\TUseAs::class => [
            'spaceBefore' => true,
            'spaceAfter' => true,
        ],
        Token\TUseClosingBrace::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
        ],
        Token\TUseComma::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
        ],
        Token\TUseConst::class => [
            'spaceAfter' => true,
        ],
        Token\TUseConstClosingBrace::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
        ],
        Token\TUseEndSemicolon::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
            'lineBreakAfter' => true,
        ],
        Token\TUseFunction::class => [
            'spaceAfter' => true,
        ],
        Token\TUseFunctionClosingBrace::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
        ],
        Token\TUseTrait::class => [
            'spaceAfter' => true,
        ],
        Token\TUseTraitAs::class => [
            'spaceBefore' => true,
            'spaceAfter' => true,
        ],
        Token\TUseTraitClosingBrace::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
            'blankLineAfter' => true,
        ],
        Token\TUseTraitComma::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
        ],
        Token\TUseTraitDoubleColon::class => [
            'spaceBefore' => false,
            'spaceAfter' => false,
        ],
        Token\TUseTraitEndSemicolon::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
            'blankLineAfter' => true,
        ],
        Token\TUseTraitOpeningBrace::class => [
            'lineBreakAfter' => true,
        ],
        Token\TUseVariablesClosingParen::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
        ],
        Token\TUseVariablesComma::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
        ],
        Token\TVariable::class => [
            'spaceAfter' => true,
        ],
        Token\TVariadicEllipsis::class => [
            'spaceAfter' => false,
        ],
        Token\TWhile::class => [
            'spaceAfter' => true,
        ],
        Token\TWhileClosingBrace::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
            'blankLineAfter' => true,
        ],
        Token\TWhileClosingBraceless::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
            'lineBreakAfter' => true,
        ],
        Token\TWhileClosingParen::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
        ],
        Token\TWhileColon::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
            'lineBreakAfter' => true,
        ],
        Token\TWhileOpeningBrace::class => [
            'lineBreakAfter' => true,
        ],
        Token\TYield::class => [
            'spaceBefore' => true,
            'spaceAfter' => true,
        ],
        Token\TYieldDoubleArrow::class => [
            'spaceBefore' => true,
            'spaceAfter' => true,
        ],
        Token\TYieldEndSemicolon::class => [
            'spaceBefore' => false,
            'spaceAfter' => true,
            'lineBreakAfter' => true,
        ],
    ];

    /**
     * @inheritdoc
     */
    public protected(set) array $rules = [
        Rule\RemoveBom::class => [],
        Rule\RejoinOrphans::class => [],
        Rule\RemoveTrailingBlankLines::class => [],
    ];

    /**
     * @inheritdoc
     */
    public protected(set) array $parseAs = [];

    /**
     * @param styles_array $styles
     * @param 'same_line'|'next_line' $classBracePosition
     * @param 'same_line'|'next_line' $functionBracePosition
     * @param 'same_line'|'next_line' $controlBracePosition
     * @param 'lower'|'upper' $keywordCase
     * @param array<int, class-string<TokenRule|LineRule>>|array<class-string<TokenRule|LineRule>, array<string, mixed>> $rules
     * @param parse_as_array $parseAs
     */
    public function __construct(
        public protected(set) string $eol = "\n",
        public protected(set) int $lineLen = 84,
        public protected(set) int $indentLen = 4,
        public protected(set) bool $indentTab = false,
        string $classBracePosition = 'same_line',
        string $functionBracePosition = 'same_line',
        string $controlBracePosition = 'same_line',
        string $keywordCase = 'lower',
        bool $concatenationSpacing = true,
        bool $returnTypeColonSpacing = true,
        bool $blankLineAfterBlock = false,
        array $styles = [],
        array $rules = [],
        array $parseAs = [],
    ) {
        $this->setClassBracePosition($classBracePosition);
        $this->setFunctionBracePosition($functionBracePosition);
        $this->setControlBracePosition($controlBracePosition);
        $this->setKeywordCase($keywordCase);
        $this->setConcatenationSpacing($concatenationSpacing);
        $this->setReturnTypeColonSpacing($returnTypeColonSpacing);
        $this->setBlankLineAfterBlock($blankLineAfterBlock);

        foreach ($styles as $class => $args) {
            $this->styles[$class] ??= [];
            $this->styles[$class] = array_merge($this->styles[$class], $args);
        }

        foreach ($rules as $key => $val) {
            if (is_int($key)) {
                /** @var class-string<TokenRule|LineRule> $val */
                $this->rules[$val] = [];
            } else {
                /**
                 * @var class-string<TokenRule|LineRule> $key
                 * @var array<string, mixed> $val
                 */
                $this->rules[$key] = $val;
            }
        }

        foreach ($parseAs as $from => $to) {
            $this->parseAs[$from] = $to;
        }
    }

    /**
     * @param null|'next_line'|'same_line' $spec
     */
    protected function setClassBracePosition(?string $spec) : void
    {
        if ($spec === null) {
            return;
        }

        $args = match ($spec) {
            'same_line' => ['lineBreakBefore' => null, 'spaceBefore' => true],
            'next_line' => ['lineBreakBefore' => true, 'spaceBefore' => null],
        };

        foreach ([
            Token\TClassOpeningBrace::class,
            Token\TClasslikeOpeningBrace::class,
            Token\TInterfaceOpeningBrace::class,
            Token\TEnumOpeningBrace::class,
            Token\TTraitOpeningBrace::class,
            Token\TNamespaceOpeningBrace::class,
        ] as $class) {
            $this->styles[$class] = array_merge($this->styles[$class], $args);
        }
    }

    protected function setConcatenationSpacing(?bool $spec) : void
    {
        if ($spec === null) {
            return;
        }

        $args = match ($spec) {
            false => ['spaceBefore' => false, 'spaceAfter' => false],
            true => ['spaceBefore' => null, 'spaceAfter' => null],
        };

        $class = Token\TDot::class;
        $this->styles[$class] ??= [];
        $this->styles[$class] = array_merge($this->styles[$class], $args);
    }

    /**
     * @param null|'next_line'|'same_line' $spec
     */
    protected function setControlBracePosition(?string $spec) : void
    {
        if ($spec === null) {
            return;
        }

        $args = match ($spec) {
            'next_line' => ['lineBreakBefore' => true],
            'same_line' => ['lineBreakBefore' => null],
        };

        foreach ([
            Token\TIfOpeningBrace::class,
            Token\TElseOpeningBrace::class,
            Token\TElseifOpeningBrace::class,
            Token\TForOpeningBrace::class,
            Token\TForeachOpeningBrace::class,
            Token\TDoOpeningBrace::class,
            Token\TCatchOpeningBrace::class,
            Token\TFinallyOpeningBrace::class,
            Token\TMatchOpeningBrace::class,
            Token\TSwitchOpeningBrace::class,
            Token\TWhileOpeningBrace::class,
            Token\TAnonymousOpeningBrace::class,
            Token\TTryOpeningBrace::class,
        ] as $class) {
            $this->styles[$class] = array_merge($this->styles[$class], $args);
        }

        $args = match ($spec) {
            'next_line' => ['spaceAfter' => null, 'lineBreakAfter' => true],
            'same_line' => ['spaceAfter' => true, 'lineBreakAfter' => null],
        };

        foreach ([
            Token\TCatchContinuationBrace::class,
            Token\TDoContinuationBrace::class,
            Token\TElseifContinuationBrace::class,
            Token\TIfContinuationBrace::class,
            Token\TTryContinuationBrace::class,
        ] as $class) {
            $this->styles[$class] = array_merge($this->styles[$class], $args);
        }
    }

    /**
     * @param null|'next_line'|'same_line' $spec
     */
    protected function setFunctionBracePosition(?string $spec) : void
    {
        if ($spec === null) {
            return;
        }

        $args = match ($spec) {
            'same_line' => ['lineBreakBefore' => null, 'spaceBefore' => true],
            'next_line' => ['lineBreakBefore' => true, 'spaceBefore' => null],
        };

        $class = Token\TFunctionOpeningBrace::class;
        $this->styles[$class] = array_merge($this->styles[$class], $args);
    }

    /**
     * @param null|'lower'|'upper' $spec
     */
    protected function setKeywordCase(?string $spec) : void
    {
        if ($spec === null) {
            return;
        }

        $args = match ($spec) {
            'upper' => ['case' => 'strtoupper'],
            'lower' => ['case' => 'strtolower'],
        };

        foreach ([
            Token\TArray::class,
            Token\TArrayConstruct::class,
            Token\TFalse::class,
            Token\TNull::class,
            Token\TTrue::class,
        ] as $class) {
            $this->styles[$class] = array_merge($this->styles[$class], $args);
        }
    }

    protected function setReturnTypeColonSpacing(?bool $spec) : void
    {
        if ($spec === null) {
            return;
        }

        $args = match ($spec) {
            false => ['spaceBefore' => false],
            true => ['spaceBefore' => true],
        };

        $class = Token\TReturnColon::class;
        $this->styles[$class] = array_merge($this->styles[$class], $args);
    }

    protected function setBlankLineAfterBlock(?bool $spec) : void
    {
        if ($spec === null) {
            return;
        }

        $args = match ($spec) {
            false => ['blankLineAfter' => null, 'lineBreakAfter' => true],
            true => ['blankLineAfter' => true, 'lineBreakAfter' => null],
        };

        foreach ([
            Token\TAbstractMethodEndSemicolon::class,
            Token\TCatchClosingBrace::class,
            Token\TClassClosingBrace::class,
            Token\TClasslikeClosingBrace::class,
            Token\TCommentHashedBlankLine::class,
            Token\TCommentSlashedBlankLine::class,
            Token\TCommentStarredBlankLine::class,
            Token\TConstEndSemicolon::class,
            Token\TDeclareEndSemicolon::class,
            Token\TDocCommentBlankLine::class,
            Token\TDoWhileEndSemicolon::class,
            Token\TElseClosingBrace::class,
            Token\TElseifClosingBrace::class,
            Token\TEnumCaseEndSemicolon::class,
            Token\TEnumClosingBrace::class,
            Token\TFinallyClosingBrace::class,
            Token\TForClosingBrace::class,
            Token\TForeachClosingBrace::class,
            Token\TFunctionClosingBrace::class,
            Token\TIfClosingBrace::class,
            Token\TInterfaceClosingBrace::class,
            Token\TNamespaceEndSemicolon::class,
            Token\TPropertyEndSemicolon::class,
            Token\TPropertyHooksClosingBrace::class,
            Token\TSwitchAfterCaseClosingBrace::class,
            Token\TSwitchClosingBrace::class,
            Token\TTraitClosingBrace::class,
            Token\TUseTraitClosingBrace::class,
            Token\TUseTraitEndSemicolon::class,
            Token\TWhileClosingBrace::class,
        ] as $class) {
            $this->styles[$class] = array_merge($this->styles[$class], $args);
        }
    }
}
