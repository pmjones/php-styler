<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TUseFunctionClosingBraceTest extends TTestCase
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
                use Foo\{function bar, function baz};
                CODE,
                [
                    TPhpOpeningTag::class,
                    TUse::class,
                    TUseFunction::class,
                    TQualifiedName::class,
                    TUseEndSemicolon::class,
                    TUse::class,
                    TUseFunction::class,
                    TQualifiedName::class,
                    TUseEndSemicolon::class,
                ],
            ],
        ];
    }
}
