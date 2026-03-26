<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TUseConstClosingBraceTest extends TTestCase
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
                    TQualifiedName::class,
                    TUseEndSemicolon::class,
                    TUse::class,
                    TUseConst::class,
                    TQualifiedName::class,
                    TUseEndSemicolon::class,
                ],
            ],
        ];
    }
}
