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
                    TBacktick::class,
                    TStringFragment::class,
                    TBacktick::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
