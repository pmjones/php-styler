<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TPhpOpeningTagInlineTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'basic' => [
                '<?php $foo = 1;',
                [
                    TPhpOpeningTagInline::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
