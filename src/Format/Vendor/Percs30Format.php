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
    public protected(set) array $parseAs = [
        Token\TList::class => Token\TListAsArray::class,
        Token\TArray::class => Token\TArrayAsShort::class,
        Token\TElse::class => Token\TElseAsElseIf::class,
        Token\THeredocStart::class => Token\THeredocStartAsNowdoc::class,
        Token\TPhpClosingTag::class => Token\TPhpClosingTagRemoved::class,
        Token\TVariable::class => Token\TVariableWithExplicitInterpolation::class,
    ];

    /**
     * @inheritdoc
     */
    public protected(set) array $rules = [
        Rule\RemoveBom::class => [],
        // import cleanup
        Rule\NormalizeImports::class => [],
        Rule\OrderTypes::class => [],
        Rule\MergeParenBracket::class => [],
        Rule\RejoinOrphans::class => [],
        Rule\NormalizeTrailingCommas::class => [],
        Rule\RemoveTrailingBlankLines::class => [],
        Rule\CollapseEmptyBody::class => [],
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
