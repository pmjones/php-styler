<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TGotoTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'goto' => [
                <<<'CODE'
                <?php
                goto foo;
                $bar = 'baz';
                foo:
                $zim = 'gir';
                CODE,
                [
                    TPhpOpeningTag::class,
                    TGoto::class,
                    TGotoLabel::class,
                    TSemicolon::class,
                    TVariable::class,
                    TAssign::class,
                    TStringLiteral::class,
                    TSemicolon::class,
                    TGotoLabel::class,
                    TGotoLabelColon::class,
                    TVariable::class,
                    TAssign::class,
                    TStringLiteral::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
