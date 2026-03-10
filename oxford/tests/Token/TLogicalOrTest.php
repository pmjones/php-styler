<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TLogicalOrTest extends TTestCase
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
                $foo or $bar;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TLogicalOr::class,
                    TVariable::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
