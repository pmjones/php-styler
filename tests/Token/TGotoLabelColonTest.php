<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TGotoLabelColonTest extends TTestCase
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
                goto myLabel;
                myLabel:
                $foo = 1;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TGoto::class,
                    TGotoLabel::class,
                    TSemicolon::class,
                    TGotoLabel::class,
                    TGotoLabelColon::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
