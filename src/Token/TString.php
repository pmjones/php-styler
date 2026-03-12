<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_STRING
 *
 * Syntax: parent, self, etc.
 *
 * Reference: identifiers, e.g. keywords like `parent` and `self`, function names,
 * class names and more are matched. See also T_CONSTANT_ENCAPSED_STRING.
 */
class TString extends T
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $text = strtolower($source->text);

        if ($text === 'string') {
            $parser->add($source, static::class);
            $parser->space();
            return;
        }

        $parseClass = match ($text) {
            'true' => TTrue::class,
            'false' => TFalse::class,
            'null' => TNull::class,
            'parent' => TParent::class,
            'self' => TSelf::class,
            'int' => TInt::class,
            'integer' => TInteger::class,
            'float' => TFloat::class,
            'double' => TDouble::class,
            'real' => TReal::class,
            'bool' => TBool::class,
            'boolean' => TBoolean::class,
            'void' => TVoid::class,
            'never' => TNever::class,
            'mixed' => TMixed::class,
            'iterable' => TIterable::class,
            'object' => TObject::class,
            default => null,
        };

        if ($parseClass) {
            $parser->parse($source, $parseClass);
            return;
        }

        $prev = $parser->getPrevParsed();

        $classlikeName = match (true) {
            $prev instanceof TClass => TClassName::class,
            $prev instanceof TInterface => TInterfaceName::class,
            $prev instanceof TTrait => TTraitName::class,
            $prev instanceof TEnum => TEnumName::class,
            default => null,
        };

        if ($classlikeName !== null) {
            $parser->add($source, $classlikeName);
            $parser->space();
            return;
        }

        if (
            $prev instanceof TUse
            || $prev instanceof TNamespace
            || $prev instanceof TAttribute
            || $prev instanceof TUseTrait
            || $prev instanceof TExtends
            || $prev instanceof TImplements
            || $prev instanceof TNew
            || $prev instanceof TInstanceof
            || $prev instanceof TInsteadof
            || $prev instanceof TReturnColon
            || $prev instanceof TNullable
            || $prev instanceof TParamsOpeningParen
            || $prev instanceof TReadonly
            || $prev instanceof TPublic
            || $prev instanceof TProtected
            || $prev instanceof TPrivate
            || $prev instanceof TPublicSet
            || $prev instanceof TProtectedSet
            || $prev instanceof TPrivateSet
            || $prev instanceof TIntersection
            || $prev instanceof TUnion
            || $prev instanceof TImplementsComma
            || $prev instanceof TExtendsComma
            || $prev instanceof TUseTraitComma
            || $prev instanceof TInsteadofComma
        ) {
            $parser->add($source, TUnqualifiedName::class);
            $parser->space();
            return;
        }

        if ($prev instanceof TFunction || $prev instanceof TReference) {
            $parser->add($source, TFunctionName::class);
            return;
        }

        $nestingAddClass = match ($parser->getNesting()) {
            TUseFunction::class => TFunctionName::class,
            TUseConst::class => TConstName::class,
            TDeclareDirectivesOpeningParen::class => TDeclareDirective::class,
            default => null,
        };

        if ($nestingAddClass) {
            $parser->add($source, $nestingAddClass);

            if ($nestingAddClass === TFunctionName::class) {
            } else {
                $parser->space();
            }

            return;
        }

        if ($parser->atNesting(TPropertyHooksOpeningBrace::class)) {
            $hookClass = match ($text) {
                'get' => TPropertyHookGet::class,
                'set' => TPropertyHookSet::class,
                default => null,
            };

            if ($hookClass) {
                $parser->parse($source, $hookClass);
                return;
            }
        }

        $prevAddClass = match (true) {
            $prev instanceof TConst => TConstantName::class,
            $prev instanceof TEnumCase => TEnumCaseName::class,
            $prev instanceof TUseAs => TUseAlias::class,
            $prev instanceof TUseTraitAs => TTraitAlias::class,
            default => null,
        };

        if ($prevAddClass) {
            $parser->add($source, $prevAddClass);
            $parser->space();
            return;
        }

        if ($prev instanceof TUseComma || $prev instanceof TUseOpeningBrace) {
            $parser->add($source, TUnqualifiedName::class);
            $parser->space();
            return;
        }

        if ($parser->getNextSource()?->is(T_DOUBLE_COLON)) {
            $parser->add($source, TUnqualifiedName::class);
            $parser->space();
            return;
        }

        if (
            $parser->getNextSource()?->is(':')
            && $parser->atNesting(TArgsOpeningParen::class)
        ) {
            $parser->add($source, TNamedArgName::class);
            $parser->space();
            return;
        }

        if ($prev instanceof TGoto || $parser->getNextSource()?->is(':')) {
            $parser->add($source, TGotoLabel::class);
            $parser->space();
            return;
        }

        if ($parser->getNextSource()?->is('(')) {
            if ($prev?->is([T_OBJECT_OPERATOR, T_NULLSAFE_OBJECT_OPERATOR])) {
                if ($parser->lastSplit !== null) {
                    $parser->replaceLastSplit(new TSplitMethodCall(T_WHITESPACE, ''));
                }

                $parser->add($source, TMethodCallName::class);
                $parser->space();
            } elseif ($prev?->is(T_DOUBLE_COLON)) {
                $parser->add($source, TStaticMethodCallName::class);
                $parser->space();
            } else {
                $parser->add($source, TFunctionCallName::class);
                $parser->space();
            }

            return;
        }

        if ($prev?->is([T_OBJECT_OPERATOR, T_NULLSAFE_OBJECT_OPERATOR])) {
            $parser->add($source, TPropertyAccessName::class);
            $parser->space();
            return;
        }

        if ($prev?->is(T_DOUBLE_COLON)) {
            $parser->add($source, TStaticMemberName::class);
            $parser->space();
            return;
        }

        $parser->add($source, TUnknownString::class);
        $parser->space();
    }
}
