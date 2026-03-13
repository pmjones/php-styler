<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TUseCommaTest extends TTestCase
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
                use Foo, Bar;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TUse::class,
                    TUnqualifiedName::class,
                    TUseComma::class,
                    TUnqualifiedName::class,
                    TUseEndSemicolon::class,
                ],
            ],
        ];
    }
}
