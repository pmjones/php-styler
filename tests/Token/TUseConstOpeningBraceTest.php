<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TUseConstOpeningBraceTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'basic' => [
                <<<'CODE'
                <?php
                use const Foo\{BAR, BAZ};
                CODE,
                [
                    TPhpOpeningTag::class,
                    TUse::class,
                    TUseConst::class,
                    TConstName::class,
                    TNamespaceSeparator::class,
                    TUseConstOpeningBrace::class,
                    TConstName::class,
                    TUseComma::class,
                    TConstName::class,
                    TUseConstClosingBrace::class,
                    TUseEndSemicolon::class,
                ],
            ],
        ];
    }
}
