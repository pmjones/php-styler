<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TClasslikeClosingBraceTest extends TTestCase
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
                enum Foo { case Bar; }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TEnum::class,
                    TEnumName::class,
                    TEnumOpeningBrace::class,
                    TEnumCase::class,
                    TEnumCaseName::class,
                    TEnumCaseEndSemicolon::class,
                    TEnumClosingBrace::class,
                ],
            ],
        ];
    }
}
