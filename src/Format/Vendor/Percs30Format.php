<?php
declare(strict_types=1);

namespace PhpStyler\Format\Vendor;

use PhpStyler\Format\DeclarationFormat;
use PhpStyler\Rule;
use PhpStyler\Token;

class Percs30Format extends DeclarationFormat
{
    /**
     * @inheritdoc
     */
    public protected(set) array $parses = [
        Token\TList::class => Token\TListAsArray::class,
        Token\TArray::class => Token\TArrayAsShort::class,
        Token\TElse::class => Token\TElseAsElseIf::class,
    ];

    /**
     * @inheritdoc
     */
    public protected(set) array $rules = [
        Rule\RemoveBom::class => [],
        Rule\RemovePhpClosingTag::class => [],
        Rule\AddControlBraces::class => [],
        Rule\ExpandImports::class => [],
        Rule\ExpandTraitUse::class => [],
        Rule\ExpandConstants::class => [],
        Rule\ExpandProperties::class => [],
        Rule\RemoveImportLeadingBackslash::class => [],
        Rule\RemoveUnusedImports::class => [],
        Rule\OrderImports::class => [],
        Rule\ExpandAttributes::class => [],
        Rule\AddMissingVisibility::class => [],
        Rule\OrderModifiers::class => [],
        Rule\OrderTypes::class => [],
        Rule\MergeParenBracket::class => [],
        Rule\RejoinOrphans::class => [],
        Rule\NormalizeTrailingCommas::class => [],
        Rule\RemoveTrailingBlankLines::class => [],
        Rule\AddInstantiationParentheses::class => [],
        Rule\AddExitParentheses::class => [],
        Rule\RemoveEmptyAnonymousClassParens::class => [],
        Rule\RemoveEmptyAttributeParens::class => [],
        Rule\ConvertHeredocToNowdoc::class => [],
        Rule\ConvertImplicitInterpolation::class => [],
        Rule\CollapseEmptyBody::class => [],
        Rule\RemoveParensFromLanguageConstructs::class => [],
        Rule\NormalizeMemberSpacing::class => [],
    ];

    /**
     * @inheritdoc
     */
    public function __construct(
        string $eol = "\n",
        int $lineLen = 120,
        int $indentLen = 4,
        bool $indentTab = false,
        array $styles = [],
        array $rules = [],
    ) {
        $percsStyles = [
            Token\TPhpOpeningTagInline::class => [
                'spaceAfter' => false,
                'lineBreakAfter' => true,
            ],
        ];

        foreach ($styles as $class => $args) {
            $percsStyles[$class] = array_merge($percsStyles[$class] ?? [], $args);
        }

        parent::__construct(
            eol: $eol,
            lineLen: $lineLen,
            indentLen: $indentLen,
            indentTab: $indentTab,
            styles: $percsStyles,
            rules: $rules,
        );
        $this->setReturnTypeColonSpacing(false);
    }
}
