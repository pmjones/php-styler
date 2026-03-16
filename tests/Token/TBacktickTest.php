<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TBacktickTest extends TTestCase
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
                $foo = `ls`;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TBacktickOpening::class,
                    TStringFragment::class,
                    TBacktickClosing::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
