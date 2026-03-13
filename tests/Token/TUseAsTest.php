<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TUseAsTest extends TTestCase
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
                use Foo as Bar;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TUse::class,
                    TUnqualifiedName::class,
                    TUseAs::class,
                    TUseAlias::class,
                    TUseEndSemicolon::class,
                ],
            ],
        ];
    }
}
